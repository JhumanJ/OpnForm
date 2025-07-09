<template>
  <section
    class="sticky flex items-center inset-x-0 top-0 z-10 py-3 bg-white border-b border-neutral-200"
  >
    <div class="hidden md:flex flex-grow">
      <slot name="left" />
    </div>
    <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
      <div class="flex items-center justify-center gap-4">
        <div
          v-if="displayHome"
          class="flex items-center gap-4"
        >
          <NuxtLink
            class="flex items-center text-neutral-400 hover:text-neutral-500"
            :to="{ name: authenticated ? 'home' : 'index' }"
          >
            <Icon
              name="i-heroicons-home"
              class="flex-shrink-0 w-5 h-5"
            />
            <span class="sr-only">Home</span>
          </NuxtLink>
          <Icon
            name="i-heroicons-chevron-right"
            class="flex-shrink-0 w-5 h-5 text-neutral-400"
          />
        </div>

        <div
          v-for="(item, index) in path"
          :key="index"
          class="flex items-center gap-4"
        >
          <NuxtLink
            v-if="item.to"
            class="flex items-center text-sm font-semibold text-neutral-500 hover:text-neutral-700 truncate"
            :to="item.to"
          >
            {{ item.name }}
          </NuxtLink>
          <div
            v-else
            class="flex items-center text-sm font-semibold sm:w-full w-36 text-blue-500 truncate"
          >
            {{ item.name }}
          </div>
          <Icon
            v-if="index !== path.length - 1"
            name="i-heroicons-chevron-right"
            class="flex-shrink-0 w-5 h-5 text-neutral-400"
          />
        </div>
      </div>
    </div>
    <div class="hidden md:flex flex-grow justify-end">
      <slot name="right" />
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props
defineProps({
  /**
   * Array of path items with structure:
   * [{ name: string, to?: RouteLocationRaw }]
   */
  path: { 
    type: Array, 
    default: () => [] 
  },
})

// Composables
const { isAuthenticated } = useIsAuthenticated()

// Reactive data
const displayHome = ref(true)

// Computed properties
const authenticated = computed(() => isAuthenticated.value)
</script>
