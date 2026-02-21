<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $roleIds = $this->data['role_ids'] ?? [];
        $roles = Role::whereIn('id', $roleIds)->get();
        $this->record->syncRoles($roles);
    }
}
