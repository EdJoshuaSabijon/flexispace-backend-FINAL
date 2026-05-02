<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add cancellation fields to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancel_reason')->nullable()->after('status');
            $table->text('rejection_reason')->nullable()->after('cancel_reason');
        });

        // Add defect image to returns
        Schema::table('returns', function (Blueprint $table) {
            $table->string('defect_image')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'rejection_reason']);
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn('defect_image');
        });
    }
};
