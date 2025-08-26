<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade'); 
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->onDelete('set null');
            $table->string('room_number');
            $table->text('description')->nullable();
            $table->string('size')->nullable();
            $table->integer('floor')->nullable();
            $table->string('status')->default('available');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['property_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};