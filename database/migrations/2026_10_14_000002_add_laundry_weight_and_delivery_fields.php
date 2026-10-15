<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->decimal('pickup_fee', 12, 2)->default(5000);
        });

        Schema::table('laundry_transactions', function (Blueprint $table) {
            $table->string('delivery_method', 20)->default('dropoff');
            $table->decimal('pickup_fee', 12, 2)->default(0);
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->decimal('service_rate', 12, 2)->nullable();
            $table->timestamp('weighed_at')->nullable();
            $table->string('delivery_status', 20)->default('waiting');
        });
    }

    public function down(): void
    {
        Schema::table('laundry_transactions', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'pickup_fee', 'weight_kg', 'service_rate', 'weighed_at', 'delivery_status']);
        });

        Schema::table('laundries', function (Blueprint $table) {
            $table->dropColumn('pickup_fee');
        });
    }
};
