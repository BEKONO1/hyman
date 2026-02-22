<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProviderSubscriptionAlter extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('provider_subscriptions', function (Blueprint $table) {
            $table->string('active_in_app_purchase_identifier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_subscriptions', function (Blueprint $table) {
            //
        });
    }
};
