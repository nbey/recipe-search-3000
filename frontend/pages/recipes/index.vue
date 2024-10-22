<template>
  <div class="container max-w-5xl mx-auto p-4">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
      <UInput
        v-model="searchFilters.authorEmail"
        placeholder="Author Email"
        class="w-full md:w-1/3 rounded-md p-2"
        icon="mdi-email"
        :ui="{ icon: { trailing: { pointer: '' } } }"
        @input="debouncedUpdateFilters"
      >
        <template #trailing>
          <UButton
            v-show="searchFilters.authorEmail !== ''"
            color="gray"
            variant="link"
            icon="i-heroicons-x-mark-20-solid"
            :padded="false"
            @click="resetSearchField('authorEmail')"
          />
        </template>
      </UInput>
      <UInput
        v-model="searchFilters.keyword"
        placeholder="Keyword"
        class="w-full md:w-1/3 rounded-md p-2"
        icon="mdi-key"
        :ui="{ icon: { trailing: { pointer: '' } } }"
        @input="debouncedUpdateFilters"
      >
        <template #trailing>
          <UButton
            v-show="searchFilters.keyword !== ''"
            color="gray"
            variant="link"
            icon="i-heroicons-x-mark-20-solid"
            :padded="false"
            @click="resetSearchField('keyword')"
          />
        </template>
      </UInput>
      <UInput
        v-model="searchFilters.ingredient"
        placeholder="Ingredient"
        class="w-full md:w-1/3 rounded-md p-2"
        icon="mdi-silverware-spoon"
        :ui="{ icon: { trailing: { pointer: '' } } }"
        @input="debouncedUpdateFilters"
      >
        <template #trailing>
          <UButton
            v-show="searchFilters.ingredient !== ''"
            color="gray"
            variant="link"
            icon="i-heroicons-x-mark-20-solid"
            :padded="false"
            @click="resetSearchField('ingredient')"
          />
        </template>
      </UInput>
    </div>
    <div v-if="recipes && recipes.length === 0">
      <UCard class="my-12 p-8 border border-gray-300 rounded-lg shadow-md text-center">
        <div class="border-b border-gray-200 pb-2 mb-2">
          <h3 class="text-xl font-semibold">No recipes found.</h3>
        </div>
        <div class="pt-2">
          <p class="text-sm text-gray-500">Please try your search again with different parameters</p>
        </div>
      </UCard>
    </div>
    <div v-else>
      <UCard v-for="recipe in recipes" :key="recipe.id" class="mb-4 p-4 border border-gray-300 rounded-lg shadow-md">
        <ULink
          :to="`/recipes/${recipe.slug}`"
          class="no-underline"
        >
          <div class="border-b border-gray-200 pb-2 mb-2">
            <h3 class="text-xl font-semibold hover:underline">{{ recipe.name }}</h3>
            <p class="text-sm text-gray-500">by {{ recipe.author_email }}</p>
          </div>
          <div class="pt-2">
            <p>{{ recipe.description }}</p>
          </div>
        </ULink>
      </UCard>
    </div>
    <div v-if="pagination.totalPages > 1" class="flex justify-center mt-6 fixed bottom-4 left-0 right-0 md:static md:mt-6">
      <UPagination
        v-model="pagination.currentPage"
        :total-pages="pagination.totalPages"
        :total="pagination.totalItems"
        :page-count="5"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

// State
const loading = ref(false);
const searchFilters = ref({
  authorEmail: '',
  keyword: '',
  ingredient: ''
});
const recipes = ref([]);
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
});

// Router
const route = useRoute();
const router = useRouter();

// Config
const config = useRuntimeConfig();

// Debounce Utility
const debounce = (func, wait) => {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
};

// API Call
const searchRecipes = async () => {
  let query = {
    author_email: searchFilters?.value?.authorEmail,
    keyword: searchFilters?.value?.keyword,
    ingredient: searchFilters?.value?.ingredient,
    page: pagination?.value?.currentPage,
  };

  if (!hasRouteChanged(query)) {
    return;
  }

  try {
    loading.value = true;

    const params = new URLSearchParams(query);

    const {
      data,
      meta: { current_page, last_page, total }
    } = await $fetch(`${config.public.apiBaseClient}/api/v1/recipes?${params.toString()}`, 'recipes');

    recipes.value = data;
    pagination.value = {
      currentPage: current_page,
      totalPages: last_page,
      totalItems: total,
    };
  } catch (error) {
    console.error('Error fetching recipes:', error);
    if (error.statusCode === 422) { // handle invalid request errors
      recipes.value = [];
    }
  } finally {
    loading.value = false;
  }
};

const hasRouteChanged = (newQuery) => {
  const currentQuery = route.query;
  return (
    currentQuery.authorEmail !== newQuery.authorEmail ||
    currentQuery.keyword !== newQuery.keyword ||
    currentQuery.ingredient !== newQuery.ingredient ||
    parseInt(currentQuery.page) !== newQuery.page
  );
};

// Sync filters to route query
const updateRouteWithFilters = () => {
  const query = {
    authorEmail: searchFilters.value.authorEmail || undefined,
    keyword: searchFilters.value.keyword || undefined,
    ingredient: searchFilters.value.ingredient || undefined,
  };

  // Only include the `page` query parameter if it's greater than 1
  if (pagination.value.currentPage > 1) {
    query.page = pagination.value.currentPage;
  }

  // Update the route with the cleaned query parameters
  router.push({ query });
};

// Debounced Update of Filters and Route
const debouncedUpdateFilters = debounce(() => {
  if (pagination.value.currentPage !== 1) {
    pagination.value.currentPage = 1; // Reset page to 1 on new filter
  }
  updateRouteWithFilters();
}, 800);

// Reset Search Filters
const resetSearchField = (fieldName) => {
  searchFilters.value[fieldName] = '';
  updateRouteWithFilters();
};

// Watcher for page change
watch(
  () => pagination.value.currentPage,   // Watch the current page value
  (newPage) => {
    updateRouteWithFilters();  // Update the route when the page changes
  }
);

// Watch Route Query for Changes
watch(
  () => route.query,
  (newQuery) => {
    searchFilters.value.authorEmail = newQuery.authorEmail || '';
    searchFilters.value.keyword = newQuery.keyword || '';
    searchFilters.value.ingredient = newQuery.ingredient || '';
    pagination.value.currentPage = parseInt(newQuery.page) || 1;
    searchRecipes();
  },
  { immediate: true }
);

// Initial Fetch on Mounted
onMounted(() => {
  // Set filters and pagination from route query if available
  searchFilters.value.authorEmail = route.query.authorEmail || '';
  searchFilters.value.keyword = route.query.keyword || '';
  searchFilters.value.ingredient = route.query.ingredient || '';
  pagination.value.currentPage = parseInt(route.query.page) || 1;
});
</script>
