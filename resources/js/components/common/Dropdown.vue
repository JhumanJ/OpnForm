<template>
  <div class="relative">
    <slot name="trigger"
          :toggle="toggle"
          :open="open"
          :close="close"
    />

    <collapsible v-model="isOpen" :class="dropdownClass">
      <div class="py-1 " role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <slot />
      </div>
    </collapsible>
  </div>
</template>

<script>
import { ref } from 'vue'
import Collapsible from './transitions/Collapsible.vue'

export default {
  name: 'Dropdown',
  components: { Collapsible },
  directives: {},
  props: {
    dropdownClass: {
      type: String,
      default: 'origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20'
    }
  },
  setup () {
    const isOpen = ref(false)

    const open = () => {
      isOpen.value = true
    }

    const close = () => {
      isOpen.value = false
    }

    const toggle = () => {
      isOpen.value = !isOpen.value
    }

    return {
      isOpen,
      open,
      close,
      toggle
    }
  }
}
</script>
