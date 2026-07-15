<?php

namespace Database\Seeders;

use App\Models\County;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $CountiesJson = public_path('portal/assets/json/counties.json');

        if (!File::exists($CountiesJson)) {
            $this->command->error("File not found: {$CountiesJson}");
            return;
        }

        $Data = File::get($CountiesJson);
        $counties = json_decode($Data, true);

        if (!$counties) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        foreach ($counties as $item) {
            County::updateOrCreate(
                [
                    'state_id' => $item['state_id'],
                    'name' => $item['county_name'],
                ],
                [
                    'deleted_at' => $item['deleted_at'] ?? null,
                ]
            );
        }
    }
}
