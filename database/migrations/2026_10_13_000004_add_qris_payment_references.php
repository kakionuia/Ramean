<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->nullable();
            $table->string('payment_reference')->nullable()->unique();
        });

        Schema::table('user_wallet_topups', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('user_wallet_topups', function (Blueprint $table) {
            $table->dropUnique(['payment_reference']);
            $table->dropColumn('payment_reference');
        });

        Schema::table('laundry_transactions', function (Blueprint $table) {
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['payment_method', 'payment_reference']);
        });
    }
};