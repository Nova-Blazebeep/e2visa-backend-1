<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $SatesJson = public_path('portal/assets/json/states.json');

        if (!File::exists($SatesJson)) {
            $this->command->error("File not found: {$SatesJson}");
            return;
        }

        $Data = File::get($SatesJson);
        $states = json_decode($Data, true);

        if (!$states) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        foreach ($states as $item) {
            State::Create(
                [
                    'country_id' => $item['country_id'],
                    'name' => $item['state_name']
                ],
                [
                    'deleted_at' => $item['deleted_at'] ?? null,
                ]
            );
        }
    }
}
