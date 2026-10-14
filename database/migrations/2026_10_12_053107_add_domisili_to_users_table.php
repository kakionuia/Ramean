<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->after('email'); // Alamat lengkap
            $table->string('domisili')->nullable()->after('address'); // Bogor
            $table->string('sub_domisili')->nullable()->after('domisili'); // kabupaten / kota
            $table->string('kecamatan')->nullable()->after('sub_domisili'); // Nama Kecamatan
            $table->string('wilayah_akhir')->nullable()->after('kecamatan'); // Nama Desa/Kelurahan terakhir
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'domisili', 'sub_domisili', 'kecamatan', 'wilayah_akhir']);
        });
    }
};