<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('banner_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('banner_id');
            $table->dateTime('datetime');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_type')->nullable();
            $table->string('txn_id')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('other_transaction_detail')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('banner_id')->references('id')->on('promotional_banners')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_payments');
    }
};
