<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'author_email' => $this->author_email,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'steps' => $this->steps,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}