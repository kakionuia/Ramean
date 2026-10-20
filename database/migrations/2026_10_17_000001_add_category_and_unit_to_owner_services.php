<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owner_services', function (Blueprint $table) {
            $table->string('category', 40)->default('Kiloan')->after('name');
            $table->string('unit', 12)->default('/Kg')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('owner_services', function (Blueprint $table) {
            $table->dropColumn(['category', 'unit']);
        });
    }
};
