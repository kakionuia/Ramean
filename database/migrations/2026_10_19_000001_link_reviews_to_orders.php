<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_reviews', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('laundry_id')->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->after('user_id')->unique()
                ->constrained('laundry_transactions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laundry_reviews', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique(['transaction_id']);
            $table->dropColumn(['transaction_id', 'user_id']);
        });
    }
};
