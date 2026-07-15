<?php
namespace Database\Seeders;
use App\Models\EstablishedYears;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class EstablishedYearsSeeder extends Seeder
{
    public function run(): void
    {
        $YearsJson = public_path('portal/assets/json/years.json');
        $YearjsonContent = File::get($YearsJson); 
        $years = json_decode($YearjsonContent, true); 

        foreach ($years as $item) {
            DB::table('established_years')->insertOrIgnore([
                'year' => $item['year'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}