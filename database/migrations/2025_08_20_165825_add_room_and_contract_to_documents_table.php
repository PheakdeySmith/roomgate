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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->after('user_id')
                  ->constrained('rooms')->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->after('room_id')
                  ->constrained('contracts')->nullOnDelete();
            $table->index(['room_id', 'contract_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'contract_id']);
            $table->dropForeign(['room_id']);
            $table->dropForeign(['contract_id']);
            $table->dropColumn(['room_id', 'contract_id']);
        });
    }
};
