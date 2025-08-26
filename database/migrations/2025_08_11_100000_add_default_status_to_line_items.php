<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('line_items', function (Blueprint $table) {
            // Add default values to status column if it exists
            if (Schema::hasColumn('line_items', 'status')) {
                DB::statement("ALTER TABLE line_items ALTER COLUMN status SET DEFAULT 'pending'");
                // Update any NULL values to 'pending'
                DB::statement("UPDATE line_items SET status = 'pending' WHERE status IS NULL");
            } else {
                // Add the column if it doesn't exist
                $table->string('status')->default('pending')->after('amount');
            }

            // Add default values to paid_amount column if it exists
            if (Schema::hasColumn('line_items', 'paid_amount')) {
                DB::statement("ALTER TABLE line_items ALTER COLUMN paid_amount SET DEFAULT 0");
                // Update any NULL values to 0
                DB::statement("UPDATE line_items SET paid_amount = 0 WHERE paid_amount IS NULL");
            } else {
                // Add the column if it doesn't exist
                $table->decimal('paid_amount', 10, 2)->default(0)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Since we're just setting default values, we don't need to do anything in the down method
        // If you added columns, you would remove them here
    }
};
