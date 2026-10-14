<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owner_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('duration_hours')->default(24);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['laundry_id', 'is_active']);
        });

        Schema::create('laundry_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('owner_services')->nullOnDelete();
            $table->string('customer_name');
            $table->string('service_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['laundry_id', 'created_at']);
        });

        Schema::create('laundry_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('category')->default('Operasional');
            $table->decimal('amount', 12, 2);
            $table->date('spent_at');
            $table->timestamps();
            $table->index(['laundry_id', 'spent_at']);
        });

        Schema::create('laundry_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->text('owner_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->index(['laundry_id', 'created_at']);
        });

        Schema::create('laundry_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->enum('sender', ['customer', 'owner']);
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['laundry_id', 'customer_name', 'id']);
        });

        Schema::create('laundry_work_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->unique(['laundry_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_work_hours');
        Schema::dropIfExists('laundry_messages');
        Schema::dropIfExists('laundry_reviews');
        Schema::dropIfExists('laundry_expenses');
        Schema::dropIfExists('laundry_transactions');
        Schema::dropIfExists('owner_services');
    }
};