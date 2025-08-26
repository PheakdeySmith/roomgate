<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, temporarily change pending statuses to a temporary value
        DB::statement("UPDATE line_items SET status = 'pending_temp' WHERE status = 'pending'");
        
        // Modify the column to support new statuses
        DB::statement("ALTER TABLE line_items MODIFY COLUMN status ENUM('pending', 'pending_temp', 'paid', 'partial', 'void', 'carried_forward', 'draft', 'sent', 'overdue') DEFAULT 'pending'");
        
        // Restore the temporary values
        DB::statement("UPDATE line_items SET status = 'pending' WHERE status = 'pending_temp'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Since this modifies data, the down migration should be approached carefully
        // First, update any new statuses to 'pending' to avoid data loss
        DB::statement("UPDATE line_items SET status = 'pending' WHERE status IN ('draft', 'sent', 'overdue')");
        
        // Then revert the column definition
        DB::statement("ALTER TABLE line_items MODIFY COLUMN status ENUM('pending', 'paid', 'partial', 'void', 'carried_forward') DEFAULT 'pending'");
    }
};
