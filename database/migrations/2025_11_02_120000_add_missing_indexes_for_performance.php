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
        // Contracts table indexes
        Schema::table('contracts', function (Blueprint $table) {
            $table->index('status', 'idx_contracts_status');
            $table->index('start_date', 'idx_contracts_start_date');
            $table->index('end_date', 'idx_contracts_end_date');
            $table->index(['user_id', 'status'], 'idx_contracts_user_status');
            $table->index(['room_id', 'status'], 'idx_contracts_room_status');
            $table->index(['landlord_id', 'status'], 'idx_contracts_landlord_status');
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status', 'idx_invoices_status');
            $table->index('issue_date', 'idx_invoices_issue_date');
            $table->index('due_date', 'idx_invoices_due_date');
            $table->index(['contract_id', 'status'], 'idx_invoices_contract_status');
            $table->index(['invoice_number'], 'idx_invoices_invoice_number');
        });

        // Line items table indexes
        Schema::table('line_items', function (Blueprint $table) {
            $table->index('status', 'idx_line_items_status');
            $table->index(['lineable_type', 'lineable_id'], 'idx_line_items_lineable');
            $table->index('invoice_id', 'idx_line_items_invoice_id');
        });

        // Meters table indexes
        Schema::table('meters', function (Blueprint $table) {
            $table->index('status', 'idx_meters_status');
            $table->index(['room_id', 'utility_type_id', 'status'], 'idx_meters_room_utility_status');
            $table->index('last_reading_date', 'idx_meters_last_reading_date');
        });

        // Meter readings table indexes
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->index('reading_date', 'idx_meter_readings_date');
            $table->index(['meter_id', 'reading_date'], 'idx_meter_readings_meter_date');
        });

        // Utility bills table indexes
        Schema::table('utility_bills', function (Blueprint $table) {
            $table->index('billing_period_start', 'idx_utility_bills_period_start');
            $table->index('billing_period_end', 'idx_utility_bills_period_end');
            $table->index(['contract_id', 'billing_period_end'], 'idx_utility_bills_contract_period');
            $table->index('utility_type_id', 'idx_utility_bills_utility_type');
        });

        // Properties table indexes
        Schema::table('properties', function (Blueprint $table) {
            $table->index(['landlord_id', 'status'], 'idx_properties_landlord_status');
        });

        // Rooms table indexes
        Schema::table('rooms', function (Blueprint $table) {
            $table->index('status', 'idx_rooms_status');
            $table->index(['property_id', 'status'], 'idx_rooms_property_status');
            $table->index('room_type_id', 'idx_rooms_room_type');
        });

        // User subscriptions table indexes
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->index('status', 'idx_user_subscriptions_status');
            $table->index('payment_status', 'idx_user_subscriptions_payment_status');
            $table->index('end_date', 'idx_user_subscriptions_end_date');
            $table->index(['user_id', 'status', 'end_date'], 'idx_user_subscriptions_user_status_end');
        });

        // Documents table indexes (if exists)
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->index('user_id', 'idx_documents_user_id');
                $table->index('contract_id', 'idx_documents_contract_id');
            });
        }

        // Base prices table indexes
        Schema::table('base_prices', function (Blueprint $table) {
            $table->index(['property_id', 'room_type_id'], 'idx_base_prices_property_room_type');
            $table->index('effective_date', 'idx_base_prices_effective_date');
        });

        // Utility rates table indexes
        Schema::table('utility_rates', function (Blueprint $table) {
            $table->index(['property_id', 'utility_type_id'], 'idx_utility_rates_property_utility');
            $table->index('effective_date', 'idx_utility_rates_effective_date');
        });

        // Users table additional indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('landlord_id', 'idx_users_landlord_id');
            $table->index('status', 'idx_users_status');
            $table->index('email', 'idx_users_email');
        });

        // Price overrides table indexes (if exists)
        if (Schema::hasTable('price_overrides')) {
            Schema::table('price_overrides', function (Blueprint $table) {
                $table->index('room_id', 'idx_price_overrides_room_id');
                $table->index(['start_date', 'end_date'], 'idx_price_overrides_dates');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop contracts indexes
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex('idx_contracts_status');
            $table->dropIndex('idx_contracts_start_date');
            $table->dropIndex('idx_contracts_end_date');
            $table->dropIndex('idx_contracts_user_status');
            $table->dropIndex('idx_contracts_room_status');
            $table->dropIndex('idx_contracts_landlord_status');
        });

        // Drop invoices indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_status');
            $table->dropIndex('idx_invoices_issue_date');
            $table->dropIndex('idx_invoices_due_date');
            $table->dropIndex('idx_invoices_contract_status');
            $table->dropIndex('idx_invoices_invoice_number');
        });

        // Drop line items indexes
        Schema::table('line_items', function (Blueprint $table) {
            $table->dropIndex('idx_line_items_status');
            $table->dropIndex('idx_line_items_lineable');
            $table->dropIndex('idx_line_items_invoice_id');
        });

        // Drop meters indexes
        Schema::table('meters', function (Blueprint $table) {
            $table->dropIndex('idx_meters_status');
            $table->dropIndex('idx_meters_room_utility_status');
            $table->dropIndex('idx_meters_last_reading_date');
        });

        // Drop meter readings indexes
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropIndex('idx_meter_readings_date');
            $table->dropIndex('idx_meter_readings_meter_date');
        });

        // Drop utility bills indexes
        Schema::table('utility_bills', function (Blueprint $table) {
            $table->dropIndex('idx_utility_bills_period_start');
            $table->dropIndex('idx_utility_bills_period_end');
            $table->dropIndex('idx_utility_bills_contract_period');
            $table->dropIndex('idx_utility_bills_utility_type');
        });

        // Drop properties indexes
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('idx_properties_landlord_status');
        });

        // Drop rooms indexes
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_status');
            $table->dropIndex('idx_rooms_property_status');
            $table->dropIndex('idx_rooms_room_type');
        });

        // Drop user subscriptions indexes
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_user_subscriptions_status');
            $table->dropIndex('idx_user_subscriptions_payment_status');
            $table->dropIndex('idx_user_subscriptions_end_date');
            $table->dropIndex('idx_user_subscriptions_user_status_end');
        });

        // Drop documents indexes (if exists)
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropIndex('idx_documents_user_id');
                $table->dropIndex('idx_documents_contract_id');
            });
        }

        // Drop base prices indexes
        Schema::table('base_prices', function (Blueprint $table) {
            $table->dropIndex('idx_base_prices_property_room_type');
            $table->dropIndex('idx_base_prices_effective_date');
        });

        // Drop utility rates indexes
        Schema::table('utility_rates', function (Blueprint $table) {
            $table->dropIndex('idx_utility_rates_property_utility');
            $table->dropIndex('idx_utility_rates_effective_date');
        });

        // Drop users additional indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_landlord_id');
            $table->dropIndex('idx_users_status');
            $table->dropIndex('idx_users_email');
        });

        // Drop price overrides indexes (if exists)
        if (Schema::hasTable('price_overrides')) {
            Schema::table('price_overrides', function (Blueprint $table) {
                $table->dropIndex('idx_price_overrides_room_id');
                $table->dropIndex('idx_price_overrides_dates');
            });
        }
    }
};