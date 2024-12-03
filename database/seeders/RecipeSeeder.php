<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    public function run()
    {
        Ingredient::factory()->count(100)->create();
        Recipe::factory()->count(50)->create();
    }
}
