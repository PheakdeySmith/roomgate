<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create tables if they don't exist
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->integer('duration_days');
                $table->json('features')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_subscriptions')) {
            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('subscription_plan_id')->constrained()->onDelete('restrict');
                $table->timestamp('start_date');
                $table->timestamp('end_date');
                $table->string('status')->default('active'); // active, canceled, expired
                $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded, trial
                $table->string('payment_method')->nullable();
                $table->string('transaction_id')->nullable();
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->text('notes')->nullable();
                $table->json('meta_data')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
