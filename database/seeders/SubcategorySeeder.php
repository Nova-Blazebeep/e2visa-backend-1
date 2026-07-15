<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = public_path('portal/assets/json/sub_categories.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at: $jsonPath");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        if (!is_array($data)) {
            $this->command->error("Invalid JSON format.");
            return;
        }

        foreach ($data as $item) {
            $categoryExists = DB::table('categories')->where('id', $item['category_id'])->exists();
            Subcategory::create([
                'category_id' => $categoryExists ? $item['category_id'] : null,
                'name' => $item['subcategory_name'],
            ]);
        }
    }
}
