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
        Schema::create('gcash_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gcash_number')->nullable();
            $table->string('gcash_qr_code')->nullable(); // path to QR code image
            $table->string('gcash_account_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gcash_settings');
    }
};
