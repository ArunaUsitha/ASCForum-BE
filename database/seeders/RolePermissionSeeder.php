<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //admin role
        Role::create([
            'id' => 1,
            'name' => \Config::get('app.access.role.admin'),
            'description' => 'Administrator',
        ]);


        //user role
        Role::create([
            'name' => \Config::get('app.access.role.user'),
            'description' => 'User',
        ]);


        //fetch and create all the cofigured permissions
        $permissions = \Config::get('app.access.permissions');

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'description' => $permission['desc'],
            ]);
        }

    }
}
