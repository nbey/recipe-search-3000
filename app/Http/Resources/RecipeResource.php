<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'author_email' => $this->author ? $this->author->email : null,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ingredients' => $this->ingredients->map(function (Ingredient $ingredient) {
                return [
                    'name' => $ingredient->name,
                    'amount' => $ingredient->pivot->amount,
                    'unit' => $ingredient->pivot->unit,
                ];
            }),
            'steps' => $this->steps->sortBy('sort_order', SORT_NUMERIC)->values()->map(function (Step $step) {
                return [
                    'sort_order' => $step->sort_order,
                    'description' => $step->description,
                ];
            }),
        ];
    }
}