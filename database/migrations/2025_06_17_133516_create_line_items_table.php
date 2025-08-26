<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', allowed: ['pending', 'paid', 'partial', 'void', 'carried_forward'])->default('pending');
            $table->nullableMorphs('lineable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};