<template>
  <section class="sticky flex items-center inset-x-0 top-0 z-20 py-3 bg-white border-b border-gray-200">
    <div class="hidden md:flex flex-grow">
      <slot name="left" />
    </div>
    <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
      <div class="flex items-center justify-center space-x-4">
        <div v-if="displayHome" class="flex items-center">
          <NuxtLink class="text-gray-400 hover:text-gray-500" :to="{ name: (authenticated) ? 'home' : 'index' }">
            <svg class="flex-shrink-0 w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd"
                    d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z"
                    clip-rule="evenodd"
              />
            </svg>
            <span class="sr-only">Home</span>
          </NuxtLink>
          <svg class="flex-shrink-0 w-5 h-5 text-gray-400 ml-4" viewBox="0 0 20 20" fill="currentColor"
               aria-hidden="true"
          >
            <path fill-rule="evenodd"
                  d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                  clip-rule="evenodd"
            />
          </svg>
        </div>

        <div v-for="(item,index) in path" :key="index" class="flex items-center">
          <NuxtLink v-if="item.route" class="text-sm font-semibold text-gray-500 hover:text-gray-700 truncate"
                       :to="item.route"
          >
            {{ item.label }}
          </NuxtLink>
          <div v-else class="text-sm font-semibold sm:w-full w-36 text-blue-500 truncate">
            {{ item.label }}
          </div>
          <div v-if="index!==path.length-1">
            <svg class="flex-shrink-0 w-5 h-5 text-gray-400 ml-4" viewBox="0 0 20 20" fill="currentColor"
                 aria-hidden="true"
            >
              <path fill-rule="evenodd"
                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                    clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>
    <div class="hidden md:flex flex-grow justify-end">
      <slot name="right" />
    </div>
  </section>
</template>

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth';

export default {
  name: 'Breadcrumb',
  props: {
    /**
     * route: Route object
     * label: Label
     */
    path: { type: Array }
  },

  setup () {
    const authStore = useAuthStore()
    return {
      authenticated : computed(() => authStore.check)
    }
  },

  data () {
    return {
      displayHome: true
    }
  },

  computed: {},

  mounted () {},

  methods: {}
}
</script>
