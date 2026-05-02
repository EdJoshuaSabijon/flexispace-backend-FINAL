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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('logistics_provider_id')->nullable()->after('contact_number');
            $table->enum('payment_method', ['cod', 'gcash'])->default('cod')->after('logistics_provider_id');
            $table->string('tracking_number')->nullable()->after('payment_method');
            $table->decimal('latitude', 10, 8)->nullable()->after('shipping_address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['logistics_provider_id']);
            $table->dropColumn(['logistics_provider_id', 'payment_method', 'tracking_number', 'latitude', 'longitude']);
        });
    }
};
