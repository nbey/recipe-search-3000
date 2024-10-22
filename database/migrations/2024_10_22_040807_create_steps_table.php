<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStepsTable extends Migration
{
    public function up()
    {
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->integer('sort_order');
            $table->timestamps();

            // Ensure no duplicated steps
            $table->unique(['recipe_id', 'sort_order']);

            $table->index('recipe_id');
            $table->index('sort_order');
        });
    }

    public function down()
    {
        Schema::dropIfExists('steps');
    }
}
