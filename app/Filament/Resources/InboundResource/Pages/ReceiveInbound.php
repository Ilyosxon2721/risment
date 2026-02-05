<?php

namespace App\Filament\Resources\InboundResource\Pages;

use App\Filament\Resources\InboundResource;
use App\Models\Inbound;
use App\Models\InboundItemPhoto;
use App\Services\BillingService;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ReceiveInbound extends Page
{
    protected static string $resource = InboundResource::class;

    protected static string $view = 'filament.resources.inbound-resource.pages.receive-inbound';
    
    protected static ?string $title = 'Приёмка поставки';
    
    public Inbound $record;
    
    public array $itemsData = [];
    
    public bool $useScannerCamera = false;
    public bool $useScannerUSB = false;
    
    public function mount(): void
    {
        // Load items with variants
        $this->record->load(['items.variant.product', 'items.photos']);
        
        // Initialize items data
        foreach ($this->record->items as $item) {
            $this->itemsData[$item->id] = [
                'qty_received' => $item->qty_received ?? $item->qty_planned,
                'notes' => '',
                'photos' => [],
                'issue_type' => null,
            ];
        }
        
        // Update status if needed
        if ($this->record->status === 'in_transit') {
            $this->record->update(['status' => 'receiving']);
        }
    }
    
    public function form(Form $form): Form
    {
        return $form->schema([]);
    }
    
    public function updateQuantity(int $itemId, int $quantity): void
    {
        $this->itemsData[$itemId]['qty_received'] = $quantity;
    }
    
    public function scanBarcode(string $barcode): void
    {
        // Find item by barcode
        foreach ($this->record->items as $item) {
            if ($item->variant->barcode === $barcode || $item->variant->sku_code === $barcode) {
                // Increment quantity
                $this->itemsData[$item->id]['qty_received'] = 
                    ($this->itemsData[$item->id]['qty_received'] ?? 0) + 1;
                
                Notification::make()
                    ->success()
                    ->title('Товар отсканирован')
                    ->body($item->variant->product->title . ' - ' . $item->variant->variant_name)
                    ->send();
                return;
            }
        }
        
        Notification::make()
            ->warning()
            ->title('Товар не найден')
            ->body('Штрих-код не соответствует ни одному товару в поставке')
            ->send();
    }
    
    public function saveDraft(): void
    {
        DB::transaction(function () {
            foreach ($this->record->items as $item) {
                $data = $this->itemsData[$item->id] ?? [];
                
                $item->update([
                    'qty_received' => $data['qty_received'] ?? $item->qty_planned,
                    'qty_diff' => ($data['qty_received'] ?? $item->qty_planned) - $item->qty_planned,
                ]);
            }
            
            $this->record->update([
                'received_by' => auth()->id(),
                'status' => 'receiving',
            ]);
        });
        
        Notification::make()
            ->success()
            ->title('Сохранено')
            ->body('Прогресс приёмки сохранён')
            ->send();
    }
    
    
    public function complete(\App\Services\InventoryService $inventoryService, BillingService $billingService)
    {
        $hasDiscrepancies = false;

        DB::transaction(function () use ($inventoryService, $billingService, &$hasDiscrepancies) {
            foreach ($this->record->items as $item) {
                $data = $this->itemsData[$item->id] ?? [];
                $qtyReceived = $data['qty_received'] ?? $item->qty_planned;
                $diff = $qtyReceived - $item->qty_planned;

                if ($diff != 0) {
                    $hasDiscrepancies = true;
                }

                $item->update([
                    'qty_received' => $qtyReceived,
                    'qty_diff' => $diff,
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            $status = $hasDiscrepancies ? 'completed' : 'closed';

            $this->record->update([
                'status' => $status,
                'received_at' => now(),
                'received_by' => auth()->id(),
                'has_discrepancies' => $hasDiscrepancies,
            ]);

            // If no discrepancies, update inventory immediately
            if (!$hasDiscrepancies) {
                $inventoryService->updateFromInbound($this->record);
            }

            // Accrue billing charges for inbound
            $billingService->accrueForInbound($this->record);
        });

        Notification::make()
            ->success()
            ->title('Приёмка завершена')
            ->body($hasDiscrepancies ? 'Поставка успешно принята, ожидаем подтверждения расхождений' : 'Поставка успешно принята и закрыта')
            ->send();

        $this->redirect(InboundResource::getUrl('view', ['record' => $this->record]));
    }
    
    protected function getHeaderActions(): array
    {
        return [];
    }
}
