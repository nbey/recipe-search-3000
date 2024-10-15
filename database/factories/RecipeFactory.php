<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'author_email' => $this->faker->unique()->safeEmail,
            'description' => $this->faker->paragraph,
            'slug' => Str::slug($this->faker->sentence(3)),
            'ingredients' => json_encode($this->generateIngredients()),
            'steps' => json_encode($this->generateSteps()),
        ];
    }

    private function generateIngredients()
    {
        $ingredients = [];
        $count = $this->faker->numberBetween(3, 10);

        for ($i = 0; $i < $count; $i++) {
            $ingredients[] = [
                'name' => $this->faker->word,
                'amount' => $this->faker->randomFloat(2, 0.1, 5),
                'unit' => $this->faker->randomElement(['cup', 'tbsp', 'tsp', 'oz', 'lb', 'g', 'ml']),
            ];
        }

        return $ingredients;
    }

    private function generateSteps()
    {
        $steps = [];
        $count = $this->faker->numberBetween(3, 8);

        for ($i = 0; $i < $count; $i++) {
            $steps[] = [
                'order' => $i + 1,
                'instruction' => $this->faker->sentence(10, true),
            ];
        }

        return $steps;
    }
}
