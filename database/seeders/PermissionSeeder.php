<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============== AKTIVITAS ==========================
        Permission::create(['name' => 'CASHIER_READ']);
        Permission::create(['name' => 'CASHIER_CREATE']);
        Permission::create(['name' => 'CASHIER_EDIT']);
        Permission::create(['name' => 'CASHIER_DELETE']);


        // ============== KEPEGAWAIAN ==========================
        Permission::create(['name' => 'MEMBER_READ']);
        Permission::create(['name' => 'MEMBER_CREATE']);
        Permission::create(['name' => 'MEMBER_EDIT']);
        Permission::create(['name' => 'MEMBER_DELETE']);
        Permission::create(['name' => 'MEMBER_PRINT']);

        Permission::create(['name' => 'OFFICE_READ']);
        Permission::create(['name' => 'OFFICE_CREATE']);
        Permission::create(['name' => 'OFFICE_EDIT']);
        Permission::create(['name' => 'OFFICE_DELETE']);

        Permission::create(['name' => 'OUTLETKIOS_READ']);
        Permission::create(['name' => 'OUTLETKIOS_CREATE']);
        Permission::create(['name' => 'OUTLETKIOS_EDIT']);
        Permission::create(['name' => 'OUTLETKIOS_DELETE']);

        Permission::create(['name' => 'PRODUCT_READ']);
        Permission::create(['name' => 'PRODUCT_CREATE']);
        Permission::create(['name' => 'PRODUCT_EDIT']);
        Permission::create(['name' => 'PRODUCT_DELETE']);

        Permission::create(['name' => 'CATEGORY_READ']);
        Permission::create(['name' => 'CATEGORY_CREATE']);
        Permission::create(['name' => 'CATEGORY_EDIT']);
        Permission::create(['name' => 'CATEGORY_DELETE']);

        Permission::create(['name' => 'PRODUCTATTRIBUTE_READ']);
        Permission::create(['name' => 'PRODUCTATTRIBUTE_CREATE']);
        Permission::create(['name' => 'PRODUCTATTRIBUTE_EDIT']);
        Permission::create(['name' => 'PRODUCTATTRIBUTE_DELETE']);

        Permission::create(['name' => 'REMARK_READ']);
        Permission::create(['name' => 'REMARK_CREATE']);
        Permission::create(['name' => 'REMARK_EDIT']);
        Permission::create(['name' => 'REMARK_DELETE']);


        // ================= PENGATURAN ==================================
        Permission::create(['name' => 'PENGATURAN_READ']);
        Permission::create(['name' => 'PENGATURAN_EDIT']);

        Permission::create(['name' => 'USERWEB_READ']);
        Permission::create(['name' => 'USERWEB_CREATE']);
        Permission::create(['name' => 'USERWEB_EDIT']);
        Permission::create(['name' => 'USERWEB_DELETE']);

        Permission::create(['name' => 'ROLE_READ']);
        Permission::create(['name' => 'ROLE_CREATE']);
        Permission::create(['name' => 'ROLE_EDIT']);
        Permission::create(['name' => 'ROLE_DELETE']);

        Permission::create(['name' => 'PERMISSION_READ']);
        Permission::create(['name' => 'PERMISSION_CREATE']);
        Permission::create(['name' => 'PERMISSION_EDIT']);
        Permission::create(['name' => 'PERMISSION_DELETE']);
        // ============== END PENGATURAN ==========================


        $superadmin = Role::where('name', 'superadmin')->first();
        $permissions = Permission::all();
        $superadmin->syncPermissions($permissions);
    }
}
