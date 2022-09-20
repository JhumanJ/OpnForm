<template>
  <div class="breadcrumbs flex">
    <div v-for="(item,index) in path" :key="item.label" class="flex items-center">
      <router-link v-if="item.route" class="p-1 hover:bg-blue-50 rounded-md" :to="item.route">
        {{ item.label }}
      </router-link>
      <div v-else class="p-1" :class="{'font-semibold': index===path.length-1}">
        {{ item.label }}
      </div>
      <div v-if="index!==path.length-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Breadcrumb',
  props: {
    /**
     * route: Route object
     * label: Label
     */
    path: { type: Array }
  },

  data () {
    return {}
  },

  computed: {},

  mounted () {
  },

  methods: {}
}
</script>
