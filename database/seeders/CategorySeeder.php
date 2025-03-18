<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        $categories=[
            ['name'=>'fiksi'],
            ['name'=>'non-fiksi'],
            ['name'=>'pendidikan'],
            ['name'=>'teknologi '],
            ['name'=>'sains'],
            ['name'=>'bisnis'],
            ['name'=>'sejarah'],
            ['name'=>'agama'],
            ['name'=>'psikologi'],
            ['name'=>'seni'],
            ['name'=>'hukum'],
            ['name'=>'kesehatan'],
            ['name'=>'olahraga'],
            ['name'=>'kuliner'],
            ['name'=>'petualangan'],
        ];

        collect($categories)->map(function($category){
            Category::create([
                'name'=>$category['name']
            ]);
        });
    }
}