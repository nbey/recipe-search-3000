<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeFilterRequest;
use App\Http\Resources\RecipeResource;
use App\Services\RecipeService;

class RecipeController extends Controller
{
    protected $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index(RecipeFilterRequest $request)
    {
        $filters = $request->validatedFilters();
        $perPage = $request->get('limit', 5);

        $recipes = $this->recipeService->searchRecipes($filters, $perPage);

        return RecipeResource::collection($recipes);
    }

    // todo: look into model binding
    public function show(string $slug)
    {
        $recipe = $this->recipeService->getRecipeBySlug($slug);

        return new RecipeResource($recipe);
    }
}