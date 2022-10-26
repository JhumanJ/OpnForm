<template>
  <div>
    <div class="w-full relative">
      <div class="cursor-pointer" @click="trigger">
        <slot name="title" />
      </div>
      <div class="text-gray-400 hover:text-gray-600 absolute -right-2 -top-1 cursor-pointer p-2" @click="trigger">
        <svg class="h-3 w-3 transition transform duration-500" :class="{'rotate-180':showContent}" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1L7 7L13 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
    <v-transition>
      <div v-if="showContent" class="w-full">
        <slot />
      </div>
    </v-transition>
  </div>
</template>

<script>
import VTransition from './transitions/VTransition'
export default {
  name: 'Collapse',
  components: { VTransition },
  props: {
    defaultValue: { type: Boolean, default: false }
  },
  data () {
    return {
      showContent: this.defaultValue
    }
  },
  methods: {
    trigger () {
      this.showContent = !this.showContent
      this.$emit('click', this.showContent)
    }
  }
}
</script>
