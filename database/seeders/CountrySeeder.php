<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $CountriesJson = public_path('portal/assets/json/countries.json');
        if (!File::exists($CountriesJson)) {
            $this->command->error("File not found: {$CountriesJson}");
            return;
        }

        $Data = File::get($CountriesJson);
        $countries = json_decode($Data, true);

        if (!$countries) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        foreach ($countries as $item) {
            Country::updateOrCreate(
                ['name' => $item['country_name']],
                ['deleted_at' => $item['deleted_at']]
            );
        }
    }
}
