<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration {
    public function up(): void
    {
        $timestamp = Carbon::create('2021', '09', '28', '06', '39', '37');

        $permissions = [
            [
                'id' => 149,
                'name' => 'service zone',
                'guard_name' => 'web',
                'parent_id' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 150,
                'name' => 'service zone list',
                'guard_name' => 'web',
                'parent_id' => 149,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 151,
                'name' => 'service zone add',
                'guard_name' => 'web',
                'parent_id' => 149,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 152,
                'name' => 'service zone edit',
                'guard_name' => 'web',
                'parent_id' => 149,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 153,
                'name' => 'service zone delete',
                'guard_name' => 'web',
                'parent_id' => 149,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
            if (! $exists) {
                DB::table('permissions')->insert($permission);
            }
        }
    }

    public function down(): void
    {
        $names = [
            'service zone',
            'service zone list',
            'service zone add',
            'service zone edit',
            'service zone delete',
        ];

        DB::table('permissions')->whereIn('name', $names)->delete();
    }
};
