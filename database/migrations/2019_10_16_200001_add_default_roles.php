<?php

use Illuminate\Database\Migrations\Migration;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddDefaultRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $defaultAdminPermissions = ['manage users', 'list users', 'invite users', 'list roles'];

        foreach ($defaultAdminPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $admin->givePermissionTo($permission);
        }

        Role::firstOrCreate(['name' => 'user']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
