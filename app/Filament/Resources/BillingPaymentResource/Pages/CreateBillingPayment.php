<?php

namespace App\Filament\Resources\BillingPaymentResource\Pages;

use App\Filament\Resources\BillingPaymentResource;
use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingPayment extends CreateRecord
{
    protected static string $resource = BillingPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        // If linked to invoice, check if fully paid
        if ($this->record->invoice_id && $this->record->status === BillingPayment::STATUS_COMPLETED) {
            $invoice = BillingInvoice::find($this->record->invoice_id);
            if ($invoice) {
                $totalPaid = BillingPayment::where('invoice_id', $invoice->id)
                    ->where('status', BillingPayment::STATUS_COMPLETED)
                    ->sum('amount');

                if ($totalPaid >= $invoice->total) {
                    $invoice->markPaid();
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => BillingInvoice::STATUS_PARTIALLY_PAID]);
                }
            }
        }
    }
}
