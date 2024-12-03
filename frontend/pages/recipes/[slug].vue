<template>
  <div v-if="pending" class="container max-w-sm mx-auto p-4 text-center">
    <p>Loading&hellip;</p>
    <UProgress animation="swing" />
  </div>
  <div v-else class="container max-w-3xl mx-auto p-4">
    <UCard v-if="data?.data?.id" class="p-6 border border-gray-300 rounded-lg shadow-md">
      <div class="border-b border-gray-200 pb-4 mb-4">
        <h1 class="text-3xl font-bold mb-2">{{ data.data.name }}</h1>
        <p class="text-sm text-gray-500">by {{ data.data.author_email }}</p>
      </div>
      <div class="mb-4">
        <h2 class="text-xl font-semibold mb-2">Description</h2>
        <p>{{ data.data.description }}</p>
      </div>
      <div class="mb-4">
        <h2 class="text-xl font-semibold mb-2">Ingredients</h2>
        <ul class="list-disc list-inside">
          <li v-for="ingredient in data.data.ingredients" :key="ingredient.name">
            {{ ingredient.amount }} {{ ingredient.unit }} of {{ ingredient.name }}
          </li>
        </ul>
      </div>
      <div>
        <h2 class="text-xl font-semibold mb-2">Steps</h2>
        <ol class="list-decimal list-inside">
          <li v-for="step in data.data.steps" :key="step.sort_order" class="mb-2">
            {{ step.description }}
          </li>
        </ol>
      </div>
    </UCard>
    <div v-else class="text-center text-red-500">Recipe not found!</div>
  </div>
</template>

<script setup>
  import { useRoute } from 'vue-router';

  const route = useRoute();

  const { data, pending, error } = await useApi(`/api/v1/recipes/${route.params.slug}`, 'recipeDetails');

  const recipe = data.value?.data ?? null;

  // Set up SEO meta tags
  const title = recipe ? `${recipe.name} | ${recipe.author_email}` : 'Recipe Not Found';
  useSeoMeta({
    title: () => title,
    description: () => recipe?.description || '',
  });


</script>