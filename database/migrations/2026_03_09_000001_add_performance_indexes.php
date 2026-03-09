<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Performance indexes based on observed query patterns in controllers and services.
     *
     * Each index is justified by actual usage found in the codebase.
     */
    public function up(): void
    {
        // Helper: safely add an index only if it does not already exist.
        // Works across MySQL / MariaDB / PostgreSQL / SQLite.
        $addIndex = function (string $table, array|string $columns, ?string $indexName = null) {
            $cols = (array) $columns;
            $indexName = $indexName ?: $this->buildIndexName($table, $cols);

            if ($this->indexExists($table, $indexName)) {
                return;
            }

            Schema::table($table, function (Blueprint $t) use ($cols, $indexName) {
                $t->index($cols, $indexName);
            });
        };

        // -----------------------------------------------------------------------
        // companies — manager_user_id is used as a FK lookup
        //   (Manager\DashboardController::switchCompany checks manager_user_id)
        //   Already has: index on status, subscription_plan_id, plan_status
        // -----------------------------------------------------------------------
        $addIndex('companies', 'manager_user_id');

        // -----------------------------------------------------------------------
        // skus — company_id is used in WHERE clauses (InventoryController stats,
        //   DashboardController $company->skus()->count())
        //   Already has: index on sku_code, unique on [company_id, sku_code]
        //   The unique covers company_id lookups, but a standalone index helps
        //   queries that filter only by company_id without sku_code.
        // -----------------------------------------------------------------------
        $addIndex('skus', 'company_id');

        // -----------------------------------------------------------------------
        // audit_logs — user_id is used for filtering logs by who performed actions
        //   Already has: index on [entity_type, entity_id], created_at
        //   Missing: standalone user_id index
        // -----------------------------------------------------------------------
        $addIndex('audit_logs', 'user_id');

        // -----------------------------------------------------------------------
        // invoices — status is filtered in FinanceController::invoices()
        //   and in outstanding amount calculation (whereIn status ['sent','overdue'])
        //   Already has: index on company_id
        //   Missing: status index for filtered listing
        // -----------------------------------------------------------------------
        $addIndex('invoices', 'status');

        // -----------------------------------------------------------------------
        // invoices — compound index for company + status + issue_date
        //   FinanceController sorts by issue_date and filters by company+status
        // -----------------------------------------------------------------------
        $addIndex('invoices', ['company_id', 'status', 'issue_date'], 'invoices_company_status_issue_idx');

        // -----------------------------------------------------------------------
        // payments — payment_date is used for sorting and year-based aggregation
        //   in FinanceController (whereYear, latest('payment_date'))
        //   Already has: index on company_id, invoice_id
        //   Missing: payment_date for ORDER BY and date range queries
        // -----------------------------------------------------------------------
        $addIndex('payments', 'payment_date');

        // -----------------------------------------------------------------------
        // payments — compound index for company_id + payment_date
        //   FinanceController: $company->payments()->latest('payment_date')
        //   and whereYear('payment_date', ...)
        // -----------------------------------------------------------------------
        $addIndex('payments', ['company_id', 'payment_date'], 'payments_company_payment_date_idx');

        // -----------------------------------------------------------------------
        // tickets — created_at is used for ORDER BY in TicketController::index()
        //   (orderBy('created_at', 'desc'))
        //   Compound with company_id for the full query pattern
        // -----------------------------------------------------------------------
        $addIndex('tickets', ['company_id', 'created_at'], 'tickets_company_created_idx');

        // -----------------------------------------------------------------------
        // shipments_fbo — created_at is used for ORDER BY in both Cabinet
        //   and Manager ShipmentControllers; marketplace is filtered in Manager
        //   Already has: [company_id, status]
        //   Missing: company_id + created_at for sorted listing
        // -----------------------------------------------------------------------
        $addIndex('shipments_fbo', ['company_id', 'created_at'], 'shipments_fbo_company_created_idx');

        // -----------------------------------------------------------------------
        // shipments_fbo — marketplace is filtered independently in Manager
        //   ShipmentController::index()
        // -----------------------------------------------------------------------
        $addIndex('shipments_fbo', 'marketplace');

        // -----------------------------------------------------------------------
        // inbounds — created_at used for ORDER BY in both Cabinet and Manager
        //   InboundControllers; company_id + created_at covers the full pattern
        //   Already has: [company_id, status]
        //   Missing: company_id + created_at for sorted listing
        // -----------------------------------------------------------------------
        $addIndex('inbounds', ['company_id', 'created_at'], 'inbounds_company_created_idx');

        // -----------------------------------------------------------------------
        // inbounds — standalone company_id for count queries in DashboardController
        //   (Inbound::where('company_id', ...)->whereIn('status', ...)->count())
        //   The composite [company_id, status] covers this partially, but a
        //   standalone index helps for queries without status filter.
        // -----------------------------------------------------------------------
        $addIndex('inbounds', 'company_id');

        // -----------------------------------------------------------------------
        // billing_invoice_lines — billing_invoice_id FK for eager loading
        //   ($billingInvoice->load('lines'))
        //   The foreignId constraint in billing_system_tables may auto-create
        //   an index, but we ensure it explicitly.
        // -----------------------------------------------------------------------
        $addIndex('billing_invoice_lines', 'billing_invoice_id');

        // -----------------------------------------------------------------------
        // billable_operations — operation_date is used in date-range queries
        //   in BillingReportController::buildChartData() with
        //   whereBetween('operation_date', ...)
        //   Already has: [company_id, billed], [company_id, operation_type, operation_date]
        //   Adding standalone operation_date for cross-company date queries
        // -----------------------------------------------------------------------
        $addIndex('billable_operations', 'operation_date');

        // -----------------------------------------------------------------------
        // billing_balance_transactions — company_id + created_at used for
        //   BillingReportController::transactions() with orderByDesc('created_at')
        //   Already has: [company_id, created_at]
        //   This is already covered, so we skip it.
        // -----------------------------------------------------------------------

        // -----------------------------------------------------------------------
        // billing_items — occurred_at is used for sorting in charges view
        //   (orderByDesc('occurred_at'))
        //   Already has: [company_id, period], [company_id, scope], status, etc.
        //   Missing: company_id + occurred_at for the sorted charges listing
        // -----------------------------------------------------------------------
        $addIndex('billing_items', ['company_id', 'status', 'period'], 'billing_items_company_status_period_idx');

        // -----------------------------------------------------------------------
        // billing_invoices (the v2 table) — issue_date used for ORDER BY in
        //   BillingReportController::index() (orderByDesc('issue_date'))
        //   Already has: [company_id, status], [company_id, period], status
        //   Missing: company_id + issue_date for ordering
        // -----------------------------------------------------------------------
        if (Schema::hasTable('billing_invoices') && Schema::hasColumn('billing_invoices', 'issue_date')) {
            $addIndex('billing_invoices', ['company_id', 'issue_date'], 'billing_invoices_company_issue_date_idx');
        }

        // -----------------------------------------------------------------------
        // company_discounts — company_id FK for relationship lookups
        //   (Company::applyDiscounts queries discounts by company_id)
        //   No index exists on company_id alone (only FK constraint).
        // -----------------------------------------------------------------------
        $addIndex('company_discounts', 'company_id');

        // -----------------------------------------------------------------------
        // company_discounts — date range filtering for active discounts
        //   (where starts_at <= now AND ends_at >= now)
        // -----------------------------------------------------------------------
        $addIndex('company_discounts', ['company_id', 'starts_at', 'ends_at'], 'company_discounts_company_dates_idx');

        // -----------------------------------------------------------------------
        // sellermind_account_links — company_id + status used in
        //   BillingReportController to find active/pending links
        //   Already has: index on company_id, status separately
        //   Adding compound for the combined filter
        // -----------------------------------------------------------------------
        $addIndex('sellermind_account_links', ['company_id', 'status'], 'sellermind_links_company_status_idx');

        // -----------------------------------------------------------------------
        // marketplace_credentials — company_id + is_active for active
        //   credential lookups per company
        // -----------------------------------------------------------------------
        $addIndex('marketplace_credentials', ['company_id', 'is_active'], 'marketplace_creds_company_active_idx');

        // -----------------------------------------------------------------------
        // manager_tasks — task_type is filtered independently in TaskController
        //   Already has: [company_id, status], [source, status], [source_type, source_id]
        //   Missing: company_id + task_type for type-filtered company queries
        // -----------------------------------------------------------------------
        $addIndex('manager_tasks', ['company_id', 'task_type'], 'manager_tasks_company_type_idx');

        // -----------------------------------------------------------------------
        // manager_tasks — confirmed_at used for monthly aggregation in
        //   Manager DashboardController (whereYear/whereMonth confirmed_at)
        //   and company_id + created_at for sorted listing
        // -----------------------------------------------------------------------
        $addIndex('manager_tasks', ['company_id', 'confirmed_at'], 'manager_tasks_company_confirmed_idx');
        $addIndex('manager_tasks', ['company_id', 'created_at'], 'manager_tasks_company_created_idx');

        // -----------------------------------------------------------------------
        // inbound_item_photos — inbound_item_id FK for eager loading
        //   The foreignId constraint may auto-create an index, but ensure it.
        // -----------------------------------------------------------------------
        $addIndex('inbound_item_photos', 'inbound_item_id');

        // -----------------------------------------------------------------------
        // products — created_at used for latest() ordering in ProductController
        //   Already has: [company_id, is_active], unique [company_id, article]
        //   Missing: company_id + created_at for the sorted default listing
        // -----------------------------------------------------------------------
        $addIndex('products', ['company_id', 'created_at'], 'products_company_created_idx');

        // -----------------------------------------------------------------------
        // billing_subscriptions — company_id + status used for active
        //   subscription lookups in BillingReportController
        //   Already has: [company_id, status] — skip, already covered.
        // -----------------------------------------------------------------------

        // -----------------------------------------------------------------------
        // inventory — location_code may be used for warehouse location lookups
        //   Already has: company_id, sku_id, unique [company_id, sku_id]
        //   Adding updated_at sort support for Manager InventoryController
        //   (orderBy('updated_at', 'desc'))
        // -----------------------------------------------------------------------
        $addIndex('inventory', ['company_id', 'qty_total'], 'inventory_company_qty_idx');
    }

    /**
     * Reverse the migrations — drop all indexes added above.
     */
    public function down(): void
    {
        $dropIndex = function (string $table, ?string $indexName = null, array|string|null $columns = null) {
            $indexName = $indexName ?: $this->buildIndexName($table, (array) $columns);

            if (!$this->indexExists($table, $indexName)) {
                return;
            }

            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName);
            });
        };

        $dropIndex('companies', null, 'manager_user_id');
        $dropIndex('skus', null, 'company_id');
        $dropIndex('audit_logs', null, 'user_id');
        $dropIndex('invoices', null, 'status');
        $dropIndex('invoices', 'invoices_company_status_issue_idx');
        $dropIndex('payments', null, 'payment_date');
        $dropIndex('payments', 'payments_company_payment_date_idx');
        $dropIndex('tickets', 'tickets_company_created_idx');
        $dropIndex('shipments_fbo', 'shipments_fbo_company_created_idx');
        $dropIndex('shipments_fbo', null, 'marketplace');
        $dropIndex('inbounds', 'inbounds_company_created_idx');
        $dropIndex('inbounds', null, 'company_id');
        $dropIndex('billing_invoice_lines', null, 'billing_invoice_id');
        $dropIndex('billable_operations', null, 'operation_date');
        $dropIndex('billing_items', 'billing_items_company_status_period_idx');

        if (Schema::hasTable('billing_invoices') && Schema::hasColumn('billing_invoices', 'issue_date')) {
            $dropIndex('billing_invoices', 'billing_invoices_company_issue_date_idx');
        }

        $dropIndex('company_discounts', null, 'company_id');
        $dropIndex('company_discounts', 'company_discounts_company_dates_idx');
        $dropIndex('sellermind_account_links', 'sellermind_links_company_status_idx');
        $dropIndex('marketplace_credentials', 'marketplace_creds_company_active_idx');
        $dropIndex('manager_tasks', 'manager_tasks_company_type_idx');
        $dropIndex('manager_tasks', 'manager_tasks_company_confirmed_idx');
        $dropIndex('manager_tasks', 'manager_tasks_company_created_idx');
        $dropIndex('inbound_item_photos', null, 'inbound_item_id');
        $dropIndex('products', 'products_company_created_idx');
        $dropIndex('inventory', 'inventory_company_qty_idx');
    }

    /**
     * Build a conventional Laravel index name from table and columns.
     */
    private function buildIndexName(string $table, array $columns): string
    {
        return $table . '_' . implode('_', $columns) . '_index';
    }

    /**
     * Check whether an index exists on a given table (cross-database compatible).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        try {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $results = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
                return count($results) > 0;
            }

            if ($driver === 'pgsql') {
                $results = DB::select(
                    "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                    [$table, $indexName]
                );
                return count($results) > 0;
            }

            if ($driver === 'sqlite') {
                $results = DB::select("PRAGMA index_list(\"{$table}\")");
                foreach ($results as $row) {
                    if ($row->name === $indexName) {
                        return true;
                    }
                }
                return false;
            }
        } catch (\Exception $e) {
            // If we cannot determine, let it fail naturally when creating
            return false;
        }

        return false;
    }
};
