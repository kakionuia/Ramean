<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE laundry_messages MODIFY sender ENUM('customer', 'owner', 'admin') NOT NULL");
        }
    }

    public function down(): void
    {
        DB::table('laundry_messages')->where('sender', 'admin')->update(['sender' => 'owner']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE laundry_messages MODIFY sender ENUM('customer', 'owner') NOT NULL");
        }
    }
};
