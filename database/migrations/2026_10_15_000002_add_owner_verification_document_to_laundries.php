<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->string('verification_document_path')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->dropColumn('verification_document_path');
        });
    }
};
