<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owner_services', function (Blueprint $table) {
            $table->string('promo_label')->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->date('promo_until')->nullable();
        });

        Schema::table('laundry_transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('laundry_id')->constrained()->nullOnDelete();
            $table->text('order_details')->nullable()->after('service_name');
        });

        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('user_wallet_topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 40)->default('transfer');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wallet_topups');
        Schema::dropIfExists('user_wallets');

        Schema::table('laundry_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'order_details']);
        });

        Schema::table('owner_services', function (Blueprint $table) {
            $table->dropColumn(['promo_label', 'original_price', 'promo_until']);
        });
    }
};