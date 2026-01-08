<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'Хянах самбар харах', 'group' => 'dashboard'],

            // Order Journals
            ['name' => 'order_journals.view', 'display_name' => 'Захиалгын журнал харах', 'group' => 'operations'],
            ['name' => 'order_journals.create', 'display_name' => 'Захиалга үүсгэх', 'group' => 'operations'],
            ['name' => 'order_journals.update', 'display_name' => 'Захиалга засах', 'group' => 'operations'],
            ['name' => 'order_journals.delete', 'display_name' => 'Захиалга устгах', 'group' => 'operations'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}