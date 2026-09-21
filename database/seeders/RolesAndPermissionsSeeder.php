<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public const PERMISSIONS = [
        'products.view', 'products.create', 'products.update', 'products.delete',
        'catalog.manage', // categories, brands, units, taxes, discounts
        'inventory.view', 'inventory.adjust', 'inventory.transfer',
        'pos.use',
        'sales.view', 'sales.void', 'sales.refund', 'sales.discount',
        'customers.manage',
        'suppliers.manage', 'purchases.manage',
        'shifts.cash', // manual cash in/out
        'users.manage', 'branches.manage',
        'reports.view', 'settings.manage', 'audit.view',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo(self::PERMISSIONS);

        $manager = Role::findOrCreate('manager');
        $manager->givePermissionTo(collect(self::PERMISSIONS)->reject(
            fn ($p) => in_array($p, ['users.manage', 'branches.manage', 'settings.manage'])
        )->all());

        $cashier = Role::findOrCreate('cashier');
        $cashier->givePermissionTo([
            'pos.use', 'sales.view', 'sales.discount', 'inventory.view',
            'customers.manage',
        ]);
    }
}
