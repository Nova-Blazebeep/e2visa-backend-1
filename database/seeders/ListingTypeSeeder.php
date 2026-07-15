<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\ListingType;

class ListingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $ListingTypesJson = public_path('portal/assets/json/listing_types.json');

        if (!File::exists($ListingTypesJson)) {
            $this->command->error("File not found: {$ListingTypesJson}");
            return;
        }

        $data = File::get($ListingTypesJson);
        $listingTypes = json_decode($data, true);

        if (!$listingTypes) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        foreach ($listingTypes as $item) {
            ListingType::updateOrCreate(
                ['listing_type' => $item['listing_type']],
                ['deleted_at' => $item['deleted_at']] 
            );
        }
    }
}
