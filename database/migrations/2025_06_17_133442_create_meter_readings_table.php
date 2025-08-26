<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained('meters')->cascadeOnDelete();
            $table->decimal('reading_value', 10, 2);
            $table->date('reading_date');
            $table->foreignId('recorded_by_id')->constrained('users');
            $table->timestamps();
            $table->index('reading_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};