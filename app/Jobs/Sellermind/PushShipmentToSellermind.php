<?php

namespace App\Jobs\Sellermind;

use App\Models\ShipmentFbo;
use App\Models\SellermindAccountLink;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class PushShipmentToSellermind
{
    public function __construct(
        protected int $shipmentId,
        protected int $companyId,
    ) {}

    public function handle(): void
    {
        $link = SellermindAccountLink::where('company_id', $this->companyId)
            ->where('status', 'active')
            ->where('sync_orders', true)
            ->first();

        if (!$link) {
            return;
        }

        $shipment = ShipmentFbo::with('items.sku')->find($this->shipmentId);
        if (!$shipment) {
            return;
        }

        $items = $shipment->items->map(function ($item) {
            return [
                'sku_id' => $item->sku_id,
                'sku_code' => $item->sku?->sku_code,
                'qty' => $item->qty,
            ];
        })->toArray();

        $payload = json_encode([
            'action' => 'shipment_update',
            'link_token' => $link->link_token,
            'sellermind_company_id' => $link->sellermind_company_id,
            'risment_shipment_id' => $shipment->id,
            'data' => [
                'marketplace' => $shipment->marketplace,
                'warehouse_name' => $shipment->warehouse_name,
                'status' => $shipment->status,
                'planned_at' => $shipment->planned_at?->toIso8601String(),
                'items' => $items,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:shipments', $payload);
            Log::channel('daily')->info('Pushed shipment to sellermind:shipments queue', [
                'shipment_id' => $this->shipmentId,
                'company_id' => $this->companyId,
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to push shipment to Redis', [
                'shipment_id' => $this->shipmentId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
