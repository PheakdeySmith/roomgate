<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Groups plans like 'Pro Monthly' and 'Pro Annual' together
            $table->string('plan_group')->nullable()->after('name');

            // Stores the original monthly price to calculate discounts from
            $table->decimal('base_monthly_price', 10, 2)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['plan_group', 'base_monthly_price']);
        });
    }
};