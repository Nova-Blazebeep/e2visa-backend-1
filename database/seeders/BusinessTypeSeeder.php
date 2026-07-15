<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $businessTypesJson = public_path('portal/assets/json/business_types.json');

        if (!File::exists($businessTypesJson)) {
            $this->command->error("File not found: {$businessTypesJson}");
            return;
        }

        $data = File::get($businessTypesJson);
        $businessTypes = json_decode($data, true);

        if (!$businessTypes) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        foreach ($businessTypes as $item) {
            BusinessType::updateOrCreate(
                ['business_type' => $item['business_type']],
                ['deleted_at' => $item['deleted_at']]
            );
        }
    }
}
