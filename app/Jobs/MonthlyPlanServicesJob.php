<?php

namespace App\Jobs;

use App\Models\BillingBalance;
use App\Models\BillingItem;
use App\Models\BillingSubscription;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Runs monthly: charges BillingBalance for marketplace services
 * and fixed-price service addons linked to each company's subscription plan.
 */
class MonthlyPlanServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $period;
    protected ?int $companyId;

    public function __construct(?string $period = null, ?int $companyId = null)
    {
        $this->period    = $period ?? now()->format('Y-m');
        $this->companyId = $companyId;
    }

    public function handle(): void
    {
        Log::info('MonthlyPlanServicesJob started', ['period' => $this->period]);

        $companies = $this->getCompanies();
        $totalCharged = 0;
        $companiesProcessed = 0;

        foreach ($companies as $company) {
            $plan = SubscriptionPlan::find($company->subscription_plan_id);
            if (!$plan) {
                continue;
            }

            $companyTotal = 0;
            $companyTotal += $this->chargeMarketplaceServices($company, $plan);
            $companyTotal += $this->chargeServiceAddons($company, $plan);

            if ($companyTotal > 0) {
                $chargeAmount = $company->applyDiscounts((float) $companyTotal, 'overage');
                $balance = BillingBalance::getOrCreate($company->id);
                $balance->charge(
                    $chargeAmount,
                    "Услуги тарифа за {$this->period}",
                    SubscriptionPlan::class,
                    $plan->id
                );
                $totalCharged += $chargeAmount;
                $companiesProcessed++;
            }

            Log::debug('MonthlyPlanServicesJob: company processed', [
                'company_id' => $company->id,
                'plan'       => $plan->code,
                'total'      => $companyTotal,
                'period'     => $this->period,
            ]);
        }

        Log::info('MonthlyPlanServicesJob completed', [
            'period'              => $this->period,
            'companies_processed' => $companiesProcessed,
            'total_charged'       => $totalCharged,
        ]);
    }

    private function getCompanies()
    {
        $query = Company::whereNotNull('subscription_plan_id')
            ->whereHas('billingSubscription', fn ($q) => $q->where('status', 'active'));

        if ($this->companyId) {
            $query->where('id', $this->companyId);
        }

        return $query->get();
    }

    /**
     * Accrue marketplace services included in the plan.
     * Uses idempotency so re-runs in the same month are safe.
     */
    private function chargeMarketplaceServices(Company $company, SubscriptionPlan $plan): float
    {
        $total = 0;

        foreach ($plan->marketplaceServices()->where('is_active', true)->get() as $service) {
            if ($service->price <= 0) {
                continue;
            }

            $item = BillingItem::accrue(
                companyId:        $company->id,
                scope:            'marketplace',
                titleRu:          $service->name_ru,
                titleUz:          $service->name_uz ?: $service->name_ru,
                unitPrice:        (int) $service->price,
                qty:              1,
                sourceType:       BillingItem::SOURCE_MANUAL,
                sourceId:         null,
                addonCode:        $service->code,
                comment:          "Авто: {$this->period}",
                idempotencySuffix: $this->period . '_marketplace_' . $service->id . '_' . $company->id,
                occurredAt:       now()
            );

            if ($item) {
                $total += $item->amount;
            }
        }

        return $total;
    }

    /**
     * Accrue fixed-price service addons included in the plan.
     * Skips by_category, percent, manual types (need qty/context).
     */
    private function chargeServiceAddons(Company $company, SubscriptionPlan $plan): float
    {
        $total = 0;

        foreach ($plan->serviceAddons()->where('is_active', true)->get() as $addon) {
            if ($addon->pricing_type !== 'fixed' || $addon->value <= 0) {
                continue;
            }

            $item = BillingItem::accrue(
                companyId:        $company->id,
                scope:            $addon->scope,
                titleRu:          $addon->title_ru,
                titleUz:          $addon->title_uz ?: $addon->title_ru,
                unitPrice:        (int) $addon->value,
                qty:              1,
                sourceType:       BillingItem::SOURCE_MANUAL,
                sourceId:         null,
                addonCode:        $addon->code,
                comment:          "Авто: {$this->period}",
                idempotencySuffix: $this->period . '_addon_' . $addon->id . '_' . $company->id,
                occurredAt:       now()
            );

            if ($item) {
                $total += $item->amount;
            }
        }

        return $total;
    }
}
