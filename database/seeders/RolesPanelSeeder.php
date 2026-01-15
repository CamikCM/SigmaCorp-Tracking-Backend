<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPanelSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $pInf = Permission::firstOrCreate(['name' => 'access_informatica_panel']);
        $pSup = Permission::firstOrCreate(['name' => 'access_supervisor_panel']);

        $superAdmin = Role::firstOrCreate(['name' => config('filament-shield.super_admin.name', 'super_admin')]);
        $informatica = Role::firstOrCreate(['name' => 'informatica']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $visitador = Role::firstOrCreate(['name' => 'visitador']);

        // roles base
        $informatica->givePermissionTo([$pInf, $pSup]);
        $supervisor->givePermissionTo([$pSup]);

        // super_admin normalmente no necesita permisos, entra por rol
    }
}
