<?php

namespace App\Filament\Resources\OverageRuleResource\Pages;

use App\Filament\Resources\OverageRuleResource;
use Filament\Resources\Pages\ListRecords;

class ListOverageRules extends ListRecords
{
    protected static string $resource = OverageRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
