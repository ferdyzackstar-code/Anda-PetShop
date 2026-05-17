<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {

        $orderPermissions = ['order.history', 'order.pos', 'order.confirm', 'order.receipt'];

        $entities = ['supplier', 'product', 'category', 'role', 'user', 'permission'];
        $actions = ['index', 'show', 'edit', 'delete', 'create'];

        $crudPermissions = [];
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $crudPermissions[] = $entity . '.' . $action;
            }
        }

        $reportPermissions = ['report.hourly', 'report.daily', 'report.monthly'];
        $settingPermissions = ['setting.index'];
        $purchasePermissions = ['purchase.index', 'purchase.confirm'];
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminPermissions = array_merge($crudPermissions, $reportPermissions, $settingPermissions, $purchasePermissions);
        $adminRole->syncPermissions(Permission::whereIn('name', $adminPermissions)->get());
        $kasirRole = Role::firstOrCreate(['name' => 'Kasir']);
        $kasirRole->syncPermissions(Permission::whereIn('name', $orderPermissions)->get());

        $readOnlyActions = ['index', 'show'];
        $readOnlyEntities = ['supplier', 'product', 'category', 'role', 'user', 'permission'];

        $userPermissions = [];

        foreach ($readOnlyEntities as $entity) {
            foreach ($readOnlyActions as $action) {
                $userPermissions[] = $entity . '.' . $action;
            }
        }

        $userPermissions[] = 'report.hourly';
        $userPermissions[] = 'report.daily';
        $userPermissions[] = 'report.monthly';

        $userPermissions[] = 'purchase.index';

        $userPermissions[] = 'setting.index';

        $userRole = Role::firstOrCreate(['name' => 'User']);

        $userRole->syncPermissions(Permission::whereIn('name', $userPermissions)->get());

        $this->command->info('✅ RoleSeeder berhasil di-seed!');
        $this->command->table(['Role', 'Jumlah Permission'], [['Admin', count($adminPermissions)], ['Kasir', count($orderPermissions)], ['User', count($userPermissions)]]);
    }
}
