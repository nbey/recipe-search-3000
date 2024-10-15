<template>
  <div class="container max-w-5xl mx-auto p-4">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
      <UInput
        v-model="searchFilters.authorEmail"
        placeholder="Author Email"
        class="w-full md:w-1/3 rounded-md p-2"
        icon="mdi-email"
        :ui="{ icon: { trailing: { pointer: '' } } }"
        @input="debouncedSearchRecipes"
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
        @input="debouncedSearchRecipes"
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
        @input="debouncedSearchRecipes"
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
    <div v-if="loading" class="container max-w-sm mx-auto p-4 text-center">
      <p>Loading&hellip;</p>
      <UProgress animation="swing" />
    </div>
    <div v-else>
      <div v-if="recipes.length === 0">
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
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: true,
      searchFilters: {
        authorEmail: '',
        keyword: '',
        ingredient: ''
      },
      recipes: [],
      pagination: {
        currentPage: 1,
        totalPages: 0,
        totalItems: 0,
      }
    };
  },
  watch: {
    'pagination.currentPage': 'searchRecipes'
  },
  methods: {
    debounce(func, wait) {
      let timeout;
      return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    },
    async searchRecipes() {
      try {
        this.loading = true;

        const params = new URLSearchParams({
          author_email: this.searchFilters.authorEmail,
          keyword: this.searchFilters.keyword,
          ingredient: this.searchFilters.ingredient,
          page: this.pagination.currentPage
        });

        const response = await fetch(`http://localhost:8888/api/v1/recipes?${params.toString()}`);

        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const { data: recipes, meta: { last_page: totalPages, total: totalItems } } = await response.json();

        this.recipes = recipes;
        this.pagination = {
          ...this.pagination,
          totalPages,
          totalItems,
        };
      } catch (error) {
        console.error('Error fetching recipes:', error);
      } finally {
        this.loading = false;
      }
    },
    handlePageChange(page) {
      this.pagination.currentPage = page;
    },
    resetSearchField(fieldName) {
      this.searchFilters[fieldName] = '';
      this.searchRecipes();
    }
  },
  created() {
    this.debouncedSearchRecipes = this.debounce(() => {
      if (this.pagination.currentPage !== 1) {
        // reset the current page, which triggers searchRecipes
        this.pagination.currentPage = 1;
      } else {
        this.searchRecipes();
      }
    }, 800);
  },
  mounted() {
    if (this.$route.query) {
      this.searchFilters.authorEmail = this.$route.query.authorEmail || '';
      this.searchFilters.keyword = this.$route.query.keyword || '';
      this.searchFilters.ingredient = this.$route.query.ingredient || '';
      this.pagination.currentPage = parseInt(this.$route.query.page) || 1;
    }
    this.searchRecipes();
  }

};
</script>