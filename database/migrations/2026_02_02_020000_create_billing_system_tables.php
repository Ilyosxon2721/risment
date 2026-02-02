<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Billing plans (тарифы с расценками для интеграции)
        if (!Schema::hasTable('billing_plans')) {
            Schema::create('billing_plans', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique(); // starter, business, enterprise, payg
                $table->string('name_ru');
                $table->string('name_uz')->nullable();
                $table->decimal('monthly_fee', 15, 2)->default(0); // UZS
                $table->decimal('storage_rate', 10, 2); // UZS за единицу/день
                $table->decimal('shipment_rate', 10, 2); // UZS за отгрузку FBS
                $table->decimal('receiving_rate', 10, 2); // UZS за единицу приёмки
                $table->decimal('return_rate', 10, 2)->default(0); // UZS за возврат
                $table->boolean('returns_included')->default(false);
                $table->integer('included_storage_units')->default(0);
                $table->integer('included_shipments')->default(0);
                $table->integer('included_receiving_units')->default(0);
                $table->enum('billing_model', ['subscription', 'monthly', 'payg'])->default('subscription');
                $table->boolean('is_active')->default(true);
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // 2. User subscriptions (подписки компаний на billing plan)
        if (!Schema::hasTable('billing_subscriptions')) {
            Schema::create('billing_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('billing_plan_id')->constrained('billing_plans')->onDelete('restrict');
                $table->date('started_at');
                $table->date('expires_at')->nullable();
                $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])->default('active');
                $table->timestamps();

                $table->index(['company_id', 'status']);
            });
        }

        // 3. User balances (баланс компании)
        if (!Schema::hasTable('billing_balances')) {
            Schema::create('billing_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->unique()->constrained()->onDelete('cascade');
                $table->decimal('balance', 15, 2)->default(0); // текущий баланс UZS
                $table->timestamps();
            });
        }

        // 4. Balance transactions (движение средств)
        if (!Schema::hasTable('billing_balance_transactions')) {
            Schema::create('billing_balance_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->enum('type', ['topup', 'charge', 'refund', 'adjustment']);
                $table->decimal('amount', 15, 2); // положительная для topup/refund, отрицательная для charge
                $table->decimal('balance_after', 15, 2);
                $table->string('description');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->index(['reference_type', 'reference_id'], 'bal_tx_reference_idx');
                $table->timestamps();

                $table->index(['company_id', 'created_at']);
            });
        }

        // 5. Billing invoices (счета автобиллинга — отдельно от существующих invoices)
        if (!Schema::hasTable('billing_invoices')) {
            Schema::create('billing_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('invoice_number', 30)->unique();
                $table->date('period_start');
                $table->date('period_end');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->enum('status', ['draft', 'issued', 'paid', 'overdue', 'cancelled'])->default('draft');
                $table->date('issue_date');
                $table->date('due_date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'status']);
                $table->index('period_start');
            });
        }

        // 6. Billing invoice lines (строки счёта)
        if (!Schema::hasTable('billing_invoice_lines')) {
            Schema::create('billing_invoice_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('billing_invoice_id')->constrained('billing_invoices')->onDelete('cascade');
                $table->enum('service_type', ['subscription', 'storage', 'shipment', 'receiving', 'return', 'other']);
                $table->string('description');
                $table->integer('quantity')->default(0);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // 7. Billable operations (лог операций для расчёта)
        if (!Schema::hasTable('billable_operations')) {
            Schema::create('billable_operations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->enum('operation_type', ['shipment', 'receiving', 'return', 'storage']);
                $table->integer('quantity')->default(1);
                $table->decimal('unit_cost', 10, 2)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->boolean('billed')->default(false);
                $table->foreignId('billing_invoice_id')->nullable()->constrained('billing_invoices')->onDelete('set null');
                $table->nullableMorphs('source'); // ShipmentFbo, Inbound, etc.
                $table->date('operation_date');
                $table->timestamps();

                $table->index(['company_id', 'billed']);
                $table->index(['company_id', 'operation_type', 'operation_date'], 'billable_ops_company_type_date_idx');
            });
        }

        // 8. Storage snapshots (ежедневный снимок хранения)
        if (!Schema::hasTable('storage_snapshots')) {
            Schema::create('storage_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->date('snapshot_date');
                $table->integer('total_units')->default(0); // единиц хранения
                $table->integer('total_pallets')->default(0);
                $table->decimal('total_sqm', 8, 2)->default(0); // квадратных метров
                $table->decimal('daily_cost', 15, 2)->default(0); // стоимость хранения за день
                $table->timestamps();

                $table->unique(['company_id', 'snapshot_date']);
                $table->index('snapshot_date');
            });
        }

        // Seed default billing plans (только если таблица пуста)
        if (DB::table('billing_plans')->count() === 0) {
            DB::table('billing_plans')->insert([
                [
                    'code' => 'starter',
                    'name_ru' => 'Starter',
                    'name_uz' => 'Starter',
                    'monthly_fee' => 2900000,
                    'storage_rate' => 100,
                    'shipment_rate' => 5000,
                    'receiving_rate' => 200,
                    'return_rate' => 300,
                    'returns_included' => false,
                    'included_storage_units' => 500,
                    'included_shipments' => 300,
                    'included_receiving_units' => 1000,
                    'billing_model' => 'subscription',
                    'is_active' => true,
                    'sort' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'business',
                    'name_ru' => 'Business',
                    'name_uz' => 'Business',
                    'monthly_fee' => 7900000,
                    'storage_rate' => 80,
                    'shipment_rate' => 4000,
                    'receiving_rate' => 150,
                    'return_rate' => 0,
                    'returns_included' => true,
                    'included_storage_units' => 2000,
                    'included_shipments' => 1000,
                    'included_receiving_units' => 5000,
                    'billing_model' => 'subscription',
                    'is_active' => true,
                    'sort' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'enterprise',
                    'name_ru' => 'Enterprise',
                    'name_uz' => 'Enterprise',
                    'monthly_fee' => 19900000,
                    'storage_rate' => 50,
                    'shipment_rate' => 3000,
                    'receiving_rate' => 100,
                    'return_rate' => 0,
                    'returns_included' => true,
                    'included_storage_units' => 10000,
                    'included_shipments' => 5000,
                    'included_receiving_units' => 20000,
                    'billing_model' => 'subscription',
                    'is_active' => true,
                    'sort' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'payg',
                    'name_ru' => 'Pay as you go',
                    'name_uz' => 'Pay as you go',
                    'monthly_fee' => 0,
                    'storage_rate' => 150,
                    'shipment_rate' => 6000,
                    'receiving_rate' => 300,
                    'return_rate' => 500,
                    'returns_included' => false,
                    'included_storage_units' => 0,
                    'included_shipments' => 0,
                    'included_receiving_units' => 0,
                    'billing_model' => 'payg',
                    'is_active' => true,
                    'sort' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_snapshots');
        Schema::dropIfExists('billable_operations');
        Schema::dropIfExists('billing_invoice_lines');
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('billing_balance_transactions');
        Schema::dropIfExists('billing_balances');
        Schema::dropIfExists('billing_subscriptions');
        Schema::dropIfExists('billing_plans');
    }
};
