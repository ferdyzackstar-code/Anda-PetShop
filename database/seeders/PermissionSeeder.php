<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $entities = ['supplier', 'product', 'category', 'role', 'user', 'permission'];
        $actions = ['index', 'show', 'edit', 'delete', 'create'];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $entity . '.' . $action,
                    'guard_name' => 'web',
                ]);
            }
        }

        $orderPermissions = [
            'order.history', 
            'order.pos',
            'order.confirm',
            'order.receipt',
        ];

        foreach ($orderPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $reportPermissions = [
            'report.hourly',
            'report.daily',
            'report.monthly', 
        ];

        foreach ($reportPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        Permission::firstOrCreate([
            'name' => 'setting.index',
            'guard_name' => 'web',
        ]);

        $purchasePermissions = [
            'purchase.index',
            'purchase.confirm',
        ];

        foreach ($purchasePermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ PermissionSeeder berhasil di-seed!');
    }
}
