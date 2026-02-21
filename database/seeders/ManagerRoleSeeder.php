<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ManagerRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // Create test manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@risment.uz'],
            [
                'name' => 'Test Manager',
                'phone' => '+998901234568',
                'password' => bcrypt('manager123'),
                'is_active' => true,
            ]
        );

        $manager->assignRole('manager');
    }
}
