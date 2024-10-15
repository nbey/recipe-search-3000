<template>
  <div v-if="loading" class="container max-w-sm mx-auto p-4 text-center">
    <p>Loading&hellip;</p>
    <UProgress animation="swing" />
  </div>
  <div v-else class="container max-w-3xl mx-auto p-4">
    <UCard v-if="recipe" class="p-6 border border-gray-300 rounded-lg shadow-md">
      <div class="border-b border-gray-200 pb-4 mb-4">
        <h1 class="text-3xl font-bold mb-2">{{ recipe.name }}</h1>
        <p class="text-sm text-gray-500">by {{ recipe.author_email }}</p>
      </div>
      <div class="mb-4">
        <h2 class="text-xl font-semibold mb-2">Description</h2>
        <p>{{ recipe.description }}</p>
      </div>
      <div class="mb-4">
        <h2 class="text-xl font-semibold mb-2">Ingredients</h2>
        <ul class="list-disc list-inside">
          <li v-for="ingredient in recipe.ingredients" :key="ingredient.name">
            {{ ingredient.amount }} {{ ingredient.unit }} of {{ ingredient.name }}
          </li>
        </ul>
      </div>
      <div>
        <h2 class="text-xl font-semibold mb-2">Steps</h2>
        <ol class="list-decimal list-inside">
          <li v-for="step in recipe.steps" :key="step.order" class="mb-2">
            {{ step.instruction }}
          </li>
        </ol>
      </div>
    </UCard>
    <div v-else class="text-center text-red-500">Recipe not found.</div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

export default {
  name: 'RecipeDetails',
  setup() {
    const route = useRoute();
    const recipe = ref({});
    const loading = ref(true);

    onMounted(async () => {
      try {
        const response = await fetch(`http://localhost:8888/api/v1/recipes/${route.params.slug}`);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        let { data } = await response.json();
        recipe.value = data;
      } catch (error) {
        console.error('Error fetching recipe details:', error);
      } finally {
        loading.value = false;
      }
    });

    return {
      recipe,
      loading
    };
  }
};
</script>