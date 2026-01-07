<?php

namespace App\Filament\Resources\InboundResource\Pages;

use App\Filament\Resources\InboundResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInbound extends ViewRecord
{
    protected static string $resource = InboundResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
