<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('utility_type_id')->constrained('utility_types');
            $table->decimal('rate', 10, 4);
            $table->date('effective_from');
            $table->timestamps();
            $table->unique(['property_id', 'utility_type_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_rates');
    }
};
