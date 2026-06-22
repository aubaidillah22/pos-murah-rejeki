<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Produk
            'view-products', 'create-products', 'edit-products', 'delete-products',
            'import-products', 'export-products',

            // Kategori
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',

            // Satuan
            'view-units', 'create-units', 'edit-units', 'delete-units',

            // Transaksi / POS
            'create-transactions', 'view-transactions', 'edit-transactions', 'delete-transactions',

            // Laporan
            'view-reports', 'export-reports',

            // Pelanggan
            'view-customers', 'create-customers', 'edit-customers', 'delete-customers',

            // Supplier
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',

            // Pembelian
            'view-purchase-orders', 'create-purchase-orders', 'edit-purchase-orders',
            'receive-purchase-orders',

            // Pengguna
            'view-users', 'create-users', 'edit-users', 'delete-users',

            // Outlet
            'view-outlets', 'create-outlets', 'edit-outlets', 'delete-outlets',

            // Pengaturan
            'manage-settings',

        // Stok Opname
        'view-stock-opname', 'create-stock-opname',

        // Pengeluaran
        'view-expenses', 'create-expenses', 'edit-expenses', 'delete-expenses',

            // Laporan Keuangan
            'view-cash-flow', 'view-profit-loss',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // Admin - all permissions
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Manajer
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view-stock-opname', 'create-stock-opname',
            'view-products', 'create-products', 'edit-products',
            'import-products', 'export-products',
            'view-categories', 'create-categories', 'edit-categories',
            'view-units', 'create-units', 'edit-units',
            'create-transactions', 'view-transactions', 'edit-transactions',
            'view-reports', 'export-reports',
            'view-customers', 'create-customers', 'edit-customers',
            'view-suppliers', 'create-suppliers', 'edit-suppliers',
            'view-purchase-orders', 'create-purchase-orders', 'edit-purchase-orders',
            'receive-purchase-orders',
            'view-expenses', 'create-expenses', 'edit-expenses',
            'view-cash-flow', 'view-profit-loss',
        ]);

        // Kasir
        $kasir = Role::firstOrCreate(['name' => 'Kasir', 'guard_name' => 'web']);
        $kasir->givePermissionTo([
            'view-products',
            'create-transactions', 'view-transactions',
            'view-customers', 'create-customers',
        ]);
    }
}
