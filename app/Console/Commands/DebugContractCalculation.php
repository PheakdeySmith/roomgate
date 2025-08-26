<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Illuminate\Console\Command;

class DebugContractCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:contract {contract_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug contract rental calculation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractId = $this->argument('contract_id');
        $contract = Contract::with(['room.amenities'])->findOrFail($contractId);
        
        $this->info("Contract ID: {$contract->id}");
        $this->info("Room ID: {$contract->room_id}");
        $this->info("Rent Amount: " . ($contract->rent_amount ?? 'NULL'));
        
        $roomAmenities = $contract->room->amenities;
        $this->info("Number of amenities: " . $roomAmenities->count());
        
        $this->info("Amenities details:");
        foreach ($roomAmenities as $amenity) {
            $this->info("  - {$amenity->name}: \${$amenity->amenity_price}");
        }
        
        $amenitySum = $roomAmenities->sum('amenity_price');
        $this->info("Sum of amenity prices: \${$amenitySum}");
        
        $totalMonthlyRent = (float) ($contract->rent_amount ?? 0) + $amenitySum;
        $this->info("Calculated Total Monthly Rent: \${$totalMonthlyRent}");
        
        // Check if the amenity_price field is being accessed correctly
        $this->info("\nDirect amenity_price values:");
        foreach ($roomAmenities as $amenity) {
            $this->info("  - Amenity ID {$amenity->id}: Raw value = '{$amenity->getRawOriginal('amenity_price')}'");
            $this->info("    Casted value = '{$amenity->amenity_price}'");
            $this->info("    Type: " . gettype($amenity->amenity_price));
        }
    }
}
