<?php

namespace App\Services;

use App\Models\Recipe;
use Illuminate\Pagination\LengthAwarePaginator;


class RecipeService
{
    public function getRecipeBySlug(string $slug): Recipe|null
    {
        return Recipe::where('slug', $slug)->firstOrFail();
    }

    public function getAllRecipes(int $perPage = 5)
    {
        return Recipe::paginate($perPage)
          ->withQueryString();
    }

    /**
     * Search recipes by author email, keyword, and ingredient.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function searchRecipes(array $filters, int $perPage = 15)
    {
        $query = Recipe::query();

        // Filter by author email (exact match)
        if (!empty($filters['author_email'])) {
            $query->where('author_email', $filters['author_email']);
        }

        // Filter by keyword (matches name, description, ingredients, or steps)
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by ingredient name (partial match in JSON column)
        if (!empty($filters['ingredient'])) {
          // Support for MySQL json columns
          // todo: consider alternatives
          $ingredient = $filters['ingredient'];
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(ingredients, '$[*].name')) LIKE ?", ["%{$ingredient}%"]);
        }

        // Filter by step instruction (partial match in JSON column)
        if (!empty($filters['keyword'])) {
          $keyword = $filters['keyword'];
          $query->orWhere('steps', 'like', "%\"instruction\": \"%{$keyword}%\"");
        }

        return $query->paginate($perPage)
          ->appends($filters);
    }
}