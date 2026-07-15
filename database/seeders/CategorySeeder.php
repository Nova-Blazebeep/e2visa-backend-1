<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $filePath = public_path('portal/assets/json/categories.json');

        if (!File::exists($filePath)) {
            $this->command->error("File not found: {$filePath}");
            return;
        }

        $data = File::get($filePath);
        $categories = json_decode($data, true);

        if (!$categories) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                ['name' => $category['category_name']]
            );
        }
    }
}