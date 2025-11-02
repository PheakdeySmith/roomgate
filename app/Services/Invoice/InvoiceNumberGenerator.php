<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class InvoiceNumberGenerator
{
    /**
     * Generate a unique invoice number for a landlord
     */
    public function generate($landlord): string
    {
        // Find the last invoice for this landlord
        $lastInvoice = Invoice::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->latest('id')->first();

        // Generate components
        $landlordPrefix = $this->getLandlordPrefix($landlord);
        $timestamp = now()->format('ymd');
        $sequence = $this->getNextSequence($lastInvoice);

        $invoiceNumber = sprintf('INV-%s-%s-%04d', $landlordPrefix, $timestamp, $sequence);

        Log::info("Generated invoice number: {$invoiceNumber} for landlord {$landlord->id}");

        return $invoiceNumber;
    }

    /**
     * Get landlord prefix for invoice number
     */
    protected function getLandlordPrefix($landlord): string
    {
        // Use first 2 letters of landlord name, uppercase
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $landlord->name);

        if (strlen($name) >= 2) {
            return strtoupper(substr($name, 0, 2));
        }

        // Fallback to ID if name is too short
        return 'L' . str_pad($landlord->id, 1, '0', STR_PAD_LEFT);
    }

    /**
     * Get next sequence number
     */
    protected function getNextSequence($lastInvoice): int
    {
        if (!$lastInvoice) {
            return 1;
        }

        // Extract sequence from last invoice number
        $parts = explode('-', $lastInvoice->invoice_number);

        if (count($parts) >= 4) {
            $lastSequence = (int) end($parts);
            return $lastSequence + 1;
        }

        // Fallback to ID-based sequence
        return $lastInvoice->id + 1;
    }

    /**
     * Validate invoice number format
     */
    public function isValidFormat(string $invoiceNumber): bool
    {
        // Pattern: INV-XX-YYMMDD-XXXX
        return preg_match('/^INV-[A-Z0-9]{1,2}-\d{6}-\d{4}$/', $invoiceNumber) === 1;
    }

    /**
     * Parse invoice number components
     */
    public function parseInvoiceNumber(string $invoiceNumber): array
    {
        $parts = explode('-', $invoiceNumber);

        if (count($parts) !== 4) {
            return [];
        }

        return [
            'prefix' => $parts[0],
            'landlord' => $parts[1],
            'date' => $parts[2],
            'sequence' => (int) $parts[3],
        ];
    }
}