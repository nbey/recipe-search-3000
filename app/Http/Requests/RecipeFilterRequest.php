<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeFilterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'keyword' => 'nullable|string|max:255',
            'ingredient' => 'nullable|string|max:255',
            'author_email' => 'nullable|email',
        ];
    }

    public function validatedFilters()
    {
        return $this->only(['keyword', 'ingredient', 'author_email']);
    }
}
