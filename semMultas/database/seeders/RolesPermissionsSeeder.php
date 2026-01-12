<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'Colaborator', 'Vendedor'];

        $permissions = [
            'Create user',
            'Read user',
            'Update user',
            'Delete user',

            'Export data',
            'Import data',

            'Create client',
            'Read client',
            'Update client',
            'Delete client',
            'Read client file',

            'Create process',
            'Read process',
            'Update process',
            'Delete process',
            'Refresh process date',
            'Work process',

            'Create service',
            'Read service',
            'Update service',
            'Delete service',

            'Create status',
            'Read status',
            'Update status',
            'Delete status',

            'Create roles',
            'Read roles',
            'Update roles',
            'Delete roles',

            'Upload File',
            'Delete File',

            'Create PDF Contract',
            'Create PDF Procuration',
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(['name' => $role, 'slug' => Role::uniqueSlug($role)]);
        }

        foreach ($permissions as $permission) {
            \App\Models\Permission::firstOrCreate(['name' => $permission, 'slug' => Permission::uniqueSlug($permission)]);
        }

        //relacionar o role admin com todas as permissoes
        $roleAdmin = \App\Models\Role::where('name', 'Admin')->first();
        $roleAdmin->permissions()->sync(\App\Models\Permission::all());
    }
}
