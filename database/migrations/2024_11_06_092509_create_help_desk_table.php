<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateHelpDeskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_desk', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->unsignedBigInteger('employee_id');
            $table->string('email')->unique()->nullable();
            $table->string('contact_number', 255)->nullable();
            $table->string('mode')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->nullable()->default('0');
            $table->softDeletes();
            $table->timestamps();
        });

        $permissions = [
            [
                'id' => 143,
                'name' => 'helpdesk',
                'guard_name' => 'web',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 144,
                'name' => 'helpdesk add',
                'guard_name' => 'web',
                'parent_id' => 143,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 145,
                'name' => 'helpdesk edit',
                'guard_name' => 'web',
                'parent_id' => 143,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 146,
                'name' => 'helpdesk delete',
                'guard_name' => 'web',
                'parent_id' => 143,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 147,
                'name' => 'helpdesk list',
                'guard_name' => 'web',
                'parent_id' => 143,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Insert all permissions
        DB::table('permissions')->insert($permissions);
        
        // Step 2: Assign permissions to roles
        $roles = Role::whereIn('name', ['admin', 'demo_admin', 'provider', 'handyman', 'user'])->get();
        $permissionsToAssign = Permission::whereIn('name', ['helpdesk', 'helpdesk add', 'helpdesk edit', 'helpdesk delete','helpdesk list'])->get();
        
        foreach ($roles as $role) {
            foreach ($permissionsToAssign as $permission) {
                // Check if the permission already exists for the role
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->where('role_id', $role->id)
                    ->exists();
        
                // If it doesn't exist, assign the permission to the role
                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                    ]);
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
        Schema::dropIfExists('help_desk');
    }
}
