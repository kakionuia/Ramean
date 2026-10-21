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
            $table->string('profile_photo_path')->nullable()->after('email');
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 80);
            $table->string('recipient_name', 120);
            $table->string('recipient_phone', 30)->nullable();
            $table->text('address');
            $table->string('area', 160)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_primary']);
        });

        $now = now();
        DB::table('users')->where('role', 'user')->whereNotNull('address')->where('address', '<>', '')
            ->select('id', 'name', 'phone', 'address', 'kecamatan', 'domisili')->orderBy('id')->get()
            ->each(function ($user) use ($now) {
                DB::table('user_addresses')->insert([
                    'user_id' => $user->id,
                    'label' => 'Alamat utama',
                    'recipient_name' => $user->name,
                    'recipient_phone' => $user->phone,
                    'address' => $user->address,
                    'area' => implode(', ', array_filter([$user->kecamatan, $user->domisili])) ?: null,
                    'is_primary' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo_path');
        });
    }
};
