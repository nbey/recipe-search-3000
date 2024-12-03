<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientRecipeTable extends Migration
{
    public function up()
    {
        Schema::create('ingredient_recipe', function (Blueprint $table) {
            // Foreign keys
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');

            $table->string('amount')->nullable();
            $table->string('unit', 50)->nullable();
            $table->primary(['recipe_id', 'ingredient_id']);

            $table->index('recipe_id');
            $table->index('ingredient_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredient_recipe');
    }
}
