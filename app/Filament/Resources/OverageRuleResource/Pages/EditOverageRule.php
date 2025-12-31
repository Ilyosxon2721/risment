<?php

namespace App\Filament\Resources\OverageRuleResource\Pages;

use App\Filament\Resources\OverageRuleResource;
use Filament\Resources\Pages\EditRecord;

class EditOverageRule extends EditRecord
{
    protected static string $resource = OverageRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
