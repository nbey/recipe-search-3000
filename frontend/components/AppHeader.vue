<template>
  <header class="flex w-full items-center justify-center">
    <a
      href="/recipes"
      class="no-underline"
    >
      <div class="flex m-4">
        <BookOpenIcon class="w-8 h-8 mr-3 text-green-300" />
        <h1 class="text-green-400 text-2xl font-bold">Recipe Search 3000</h1>
      </div>
    </a>
  </header>
</template>

<script setup>
import { BookOpenIcon } from '@heroicons/vue/24/solid';
</script>