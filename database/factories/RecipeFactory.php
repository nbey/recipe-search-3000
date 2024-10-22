<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition()
    {
        $name = $this->faker->sentence;
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph,
            'user_id' => $this->faker->boolean(User::count() ? 70 : 0) ? User::inRandomOrder()->first()->id : User::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Recipe $recipe) {
            // Attach ingredients to the recipe
            $ingredients = Ingredient::inRandomOrder()->take(rand(2, 5))->get();
            foreach ($ingredients as $ingredient) {
                $recipe->ingredients()->attach($ingredient->id, [
                    'amount' => $this->faker->randomFloat(2, 1, 100),
                    'unit' => $this->faker->randomElement(['grams', 'cups', 'tablespoons']),
                ]);
            }

            // Create steps for the recipe
            foreach (range(1, rand(3, 7)) as $index) {
                Step::create([
                    'recipe_id' => $recipe->id,
                    'description' => $this->faker->sentence,
                    'sort_order' => $index,
                ]);
            }
        });
    }
}
