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
        Schema::create('logistics_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., LBC, J&T
            $table->string('code')->unique(); // e.g., lbc, jnt
            $table->string('tracking_url')->nullable(); // URL template for tracking
            $table->boolean('is_active')->default(true);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistics_providers');
    }
};
