<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
            $table->enum('status', ['pending', 'active', 'rejected', 'suspended', 'inactive'])->default('active')->change();
        });

        Schema::table('laundries', function (Blueprint $table) {
            $table->string('service_city', 120)->nullable();
            $table->string('service_district', 120)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('operational_status', 20)->default('open');
            $table->boolean('pickup_available')->default(true);
        });

        DB::table('laundries')->where('pickup_fee', '<=', 0)->update(['pickup_available' => false]);
    }

    public function down(): void
    {
        DB::table('users')->whereIn('status', ['rejected', 'inactive'])->update(['status' => 'suspended']);

        Schema::table('laundries', function (Blueprint $table) {
            $table->dropColumn(['service_city', 'service_district', 'latitude', 'longitude', 'operational_status', 'pickup_available']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'suspended'])->default('active')->change();
            $table->dropColumn(['email_verified_at', 'phone_verified_at']);
        });
    }
};
