<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;

class RecipeApiFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_recipes()
    {
        Recipe::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/recipes');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data'); // page size is 5
    }

    /** @test */
    public function it_can_list_all_recipes_with_custom_page_size()
    {
        Recipe::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/recipes?limit=2');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // page size is 2
    }

    /** @test */
    public function it_can_search_recipes_by_name_keyword()
    {
        Recipe::factory()->create(['name' => 'Coho Salmon']);
        $se = Recipe::factory()->create(['name' => 'Ground Sockeye Salmon']);

        $response = $this->getJson('/api/v1/recipes?keyword=sockeye');

        // dump($response->json());
        // dd($se);

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Ground Sockeye Salmon']);
    }

    /** @test */
    public function it_can_search_recipes_by_recipe_description_keyword()
    {
        Recipe::factory()->create(['name' => 'Coho Salmon', 'description' => 'Nafis']);
        Recipe::factory()->create(['name' => 'Ground Sockeye Salmon', 'description' => 'WAC']);

        $response = $this->getJson('/api/v1/recipes?keyword=WAC');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Ground Sockeye Salmon']);
    }


    /** @test */
    public function it_can_search_recipes_by_step_description_keyword()
    {
        $recipe = Recipe::factory()->create(['name' => 'Ground Sockeye Salmon', 'description' => 'WAC']);
        $step = Step::create(['recipe_id' => $recipe->id, 'description' => 'Nafis', 'sort_order' => $recipe->steps->count() + 1]);
        Recipe::factory()->create(['name' => 'Coho Salmon', 'description' => 'Bey']);

        $response = $this->getJson('/api/v1/recipes?keyword=Nafis');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Ground Sockeye Salmon']);
    }

    /** @test */
    public function it_can_search_recipes_by_ingredient_name_keyword()
    {
        Recipe::factory()->create(['name' => 'Coho Salmon', 'description' => 'Bey']);

        $recipe = Recipe::factory()->create(['name' => 'Ground Sockeye Salmon', 'description' => 'WAC']);
        $ingredient = Ingredient::create([
            'name' => 'Nafis'
        ]);
        $recipe->ingredients()->attach($ingredient->id, [
            'amount' => 10,
            'unit' => 'kilogram'
        ]);

        $response = $this->getJson('/api/v1/recipes?keyword=Nafis');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Ground Sockeye Salmon']);
    }

    /** @test */
    public function it_can_search_recipes_by_ingredient()
    {
        Recipe::factory()->create(['name' => 'Coho Salmon', 'description' => 'Bey']);

        $recipe = Recipe::factory()->create(['name' => 'Ground Sockeye Salmon', 'description' => 'WAC']);
        $ingredient = Ingredient::create([
            'name' => 'Nafis'
        ]);
        $recipe->ingredients()->attach($ingredient->id, [
            'amount' => 10,
            'unit' => 'kilogram'
        ]);

        $response = $this->getJson('/api/v1/recipes?ingredient=fis');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Ground Sockeye Salmon']);
    }

    /** @test */
    public function it_can_search_recipes_by_author_email()
    {
        Recipe::factory()->create(['name' => 'Coho Salmon', 'description' => 'Bey']);

        $user = User::factory()->create(['email' => 'nafis@nafis.me']);
        Recipe::factory()->create(['name' => 'Ground Sockeye Salmon', 'description' => 'WAC', 'user_id' => $user->id]);

        $response = $this->getJson('/api/v1/recipes?author_email=nafis@nafis.me');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Ground Sockeye Salmon']);
    }

    /** @test */
    public function it_can_paginate_recipes()
    {
        Recipe::factory()->count(25)->create();

        $response = $this->getJson('/api/v1/recipes?page=2');

        $response->assertStatus(200)
                 ->assertJsonFragment(['current_page' => 2])
                 ->assertJsonStructure([
                     'data',
                     'meta' => ['current_page', 'last_page', 'total'],
                 ]);
    }

    /** @test */
    public function it_can_fetch_a_single_recipe_by_slug()
    {
        $recipe = Recipe::factory()->create(['slug' => 'wild-alaskan']);

        $response = $this->getJson('/api/v1/recipes/wild-alaskan');

        $response->assertStatus(200)
                 ->assertJsonFragment(['slug' => 'wild-alaskan']);
    }
}
