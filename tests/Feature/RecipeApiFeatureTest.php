<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Recipe;

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
    public function it_can_search_recipes_by_name()
    {
        Recipe::factory()->create(['name' => 'Coho Salmon']);
        Recipe::factory()->create(['name' => 'Ground Sockeye Salmon']);

        $response = $this->getJson('/api/v1/recipes?keyword=Sockeye');

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
