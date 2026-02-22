<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\HandymanType;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class AlterHandymanTypeCreatedbyAddTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('handyman_types', function (Blueprint $table) {
            if (!Schema::hasColumn('handyman_types', 'created_by')) {
                $table->integer('created_by')->unsigned()->nullable();
                $table->integer('updated_by')->unsigned()->nullable();
                $table->integer('deleted_by')->unsigned()->nullable();
            }
           
        });

        $adminId = \App\Models\User::where('user_type', 'admin')->value('id');

        // Check if an admin user ID exists, then update all existing records in handyman_types
        if ($adminId) {
            HandymanType::query()->update([
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
        }

        // $permissions = [
        //     'handymantype list',
        //     'handymantype add',
        //     'handymantype edit',
        //     'handymantype delete',
        //     'bank list',
        //     'bank add',
        //     'bank edit',
        //     'bank delete',
        // ];

        // // Create permissions if they don't exist
        // foreach ($permissions as $permission) {
        //     Permission::updateOrCreate(['name' => $permission]);
        // }

        // Find the provider role
        $providerRole = Role::where('name', 'provider')->first();
        
        if ($providerRole) {
                $permissions = Permission::whereIn('name', [
                    'handymantype list',
                    'handymantype add',
                    'handymantype edit',
                    'handymantype delete',
                    'bank list',
                    'bank add',
                    'bank edit',
                    'bank delete',
                    'servicefaq add',
                    'servicefaq edit',
                    'servicefaq delete',
                    'servicefaq list'
                ])->get();
                foreach ($permissions as $permission) {
                    $perm = Permission::where('name', $permission)->first();
                    if ($perm) {
                        // Assign the permission to the provider role
                        DB::table('role_has_permissions')->updateOrCreate(
                            [
                                'permission_id' => $perm->id,
                                'role_id' => $providerRole->id,
                            ]
                        );
                    }
                }
           
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
        Schema::table('handyman_types', function (Blueprint $table) {
            //
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_by');
        });

        $providerRole = Role::where('name', 'provider')->first();

        if ($providerRole) {
            $permissions = Permission::whereIn('name', [
                'handymantype list',
                'handymantype add',
                'handymantype edit',
                'handymantype delete',
                'bank list',
                'bank add',
                'bank edit',
                'bank delete',
            ])->get();

            foreach ($permissions as $permission) {
                DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->where('role_id', $providerRole->id)
                    ->delete();
            }
        }
    }
}
