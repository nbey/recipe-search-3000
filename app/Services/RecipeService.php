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


    /**
     * Search recipes by author email, keyword, and ingredient.
     *
     * @param array $filters An array of filters for searching recipes
     * @param int $perPage Defaults to 15
     * @return LengthAwarePaginator
     */
    public function searchRecipes(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Recipe::query();

        // Filter by author email (exact match)
        if (!empty($filters['author_email'])) {
            $query->whereHas('author', function ($q) use ($filters) {
                $q->where('email', $filters['author_email']);
            });
        }

        // Filter by keyword (matches name, description, ingredients, or steps)
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('ingredients', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('steps', function ($q) use ($keyword) {
                        $q->where('description', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter by ingredient name (partial match)
        if (!empty($filters['ingredient'])) {
            $ingredient = $filters['ingredient'];
            $query->whereHas('ingredients', function ($q) use ($ingredient) {
                $q->where('name', 'like', "%{$ingredient}%");
            });
        }

        return $query->paginate($perPage)->appends($filters);
    }
}