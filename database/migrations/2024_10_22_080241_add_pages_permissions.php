<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class AddPagesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert new permission 'data deletion request'
        DB::table('permissions')->insert([
            'id' => 142,
            'name' => 'data deletion request',
            'guard_name' => 'web',
            'parent_id' => 55,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $roles = Role::whereIn('name', ['admin', 'demo_admin'])->get();
        
        foreach ($roles as $role) {
            if ($role) {
                $permission = Permission::where('name', 'data deletion request')->first();
        
                if ($permission) {
                    $exists = DB::table('role_has_permissions')
                        ->where('permission_id', $permission->id)
                        ->where('role_id', $role->id)
                        ->exists();
        
                    if (!$exists) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                        ]);
                    }
                }
            }
        }

        $setting = DB::table('frontend_settings')->where('type', 'heder-menu-setting')->first();

        if ($setting) {
            $value = json_decode($setting->value, true);

            unset($value['home'], $value['blogs']);

            DB::table('frontend_settings')
                ->where('type', 'heder-menu-setting')
                ->update([
                    'value' => json_encode($value),
                ]);
        }
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
