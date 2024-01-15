<template>
  <svg v-if="value===true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" fill="none"
       viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
  </svg>
  <svg v-else-if="value===false" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" fill="none"
       viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
  </svg>
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      required: true
    },

  },
  mounted() {
  },

  data() {
    return {}
  },

  computed: {
  },

  methods: {},
}
</script>
