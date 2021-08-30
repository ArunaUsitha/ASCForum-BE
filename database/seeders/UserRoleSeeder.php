<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::findByName(\Config::get('app.access.role.admin'))->syncPermissions(Permission::all());

        User::find(1)->assignRole(config('app.access.role.admin'));
    }
}
