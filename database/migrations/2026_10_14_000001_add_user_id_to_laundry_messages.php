<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_messages', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('laundry_id')->constrained()->nullOnDelete();
            $table->index(['laundry_id', 'user_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table('laundry_messages', function (Blueprint $table) {
            $table->dropIndex(['laundry_id', 'user_id', 'id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};