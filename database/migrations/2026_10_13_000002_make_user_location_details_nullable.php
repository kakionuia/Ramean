<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['domisili', 'sub_domisili', 'kecamatan', 'wilayah_akhir'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->string($column)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        // Existing owner records may not have customer address details to restore.
    }
};