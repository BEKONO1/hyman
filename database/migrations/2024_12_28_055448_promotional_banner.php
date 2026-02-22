<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->enum('banner_type', ['service', 'link']);
            $table->string('banner_redirect_url')->nullable();
            $table->boolean('is_requested_banner')->default(0);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->longText('reject_reason')->nullable();
            $table->integer('duration');
            $table->decimal('charges', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'fail','refunded', 'paid'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            // $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways');
            // $table->foreign('payment_id')->references('id')->on('payments');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotional_banners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
