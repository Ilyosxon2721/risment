<?php

namespace App\Services;

use App\Models\Inbound;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update inventory quantities based on received inbound items.
     *
     * @param Inbound $inbound
     * @return void
     */
    public function updateFromInbound(Inbound $inbound): void
    {
        DB::transaction(function () use ($inbound) {
            foreach ($inbound->items as $item) {
                if ($item->qty_received > 0) {
                    $inventory = Inventory::firstOrCreate(
                        [
                            'company_id' => $inbound->company_id,
                            'product_variant_id' => $item->variant_id,
                        ],
                        [
                            'qty_total' => 0,
                            'qty_reserved' => 0,
                        ]
                    );

                    $inventory->increment('qty_total', $item->qty_received);
                }
            }
        });
    }
}
