<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_address_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_address_mappings', 'deleted_at')) {
            $table->softDeletes();
            }
        });

        Schema::table('booking_extra_charges', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_extra_charges', 'deleted_at')) {
            $table->softDeletes();
            }
        });

        Schema::table('booking_package_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_package_mappings', 'deleted_at')) {
            $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
}
