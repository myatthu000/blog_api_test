<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ["Medicine","Food","Clothes","Information Technology","News","Category 6","category 7"];
        foreach ($categories as $category){
            Category::factory()->create([
                'uuid' => fake()->uuid(),
                'title' => $category,
                'user_id' => User::query()->inRandomOrder()->first()->id,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
