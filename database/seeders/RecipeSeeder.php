<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class RecipeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            $name = $faker->unique()->words(3, true);
            $slug = Str::slug($name);

            DB::table('recipes')->insert([
                'name' => $name,
                'author_email' => $faker->email,
                'description' => $faker->paragraph,
                'slug' => $slug,
                'ingredients' => json_encode($this->generateIngredients($faker)),
                'steps' => json_encode($this->generateSteps($faker)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateIngredients($faker)
    {
        $ingredients = [];
        $count = $faker->numberBetween(3, 10);

        for ($i = 0; $i < $count; $i++) {
            $ingredients[] = [
                'name' => $faker->word,
                'amount' => $faker->randomFloat(2, 0.1, 5),
                'unit' => $faker->randomElement(['cup', 'tbsp', 'tsp', 'oz', 'lb', 'g', 'ml']),
            ];
        }

        return $ingredients;
    }

    private function generateSteps($faker)
    {
        $steps = [];
        $count = $faker->numberBetween(3, 8);

        for ($i = 0; $i < $count; $i++) {
            $steps[] = [
                'order' => $i + 1,
                'instruction' => $faker->sentence(10, true),
            ];
        }

        return $steps;
    }
}