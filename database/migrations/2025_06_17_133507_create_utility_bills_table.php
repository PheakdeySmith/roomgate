<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignId('utility_type_id')->constrained('utility_types');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->decimal('start_reading', 10, 2)->nullable();
            $table->decimal('end_reading', 10, 2)->nullable();
            $table->decimal('consumption', 10, 2)->nullable();
            $table->decimal('rate_applied', 10, 4)->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_bills');
    }
};