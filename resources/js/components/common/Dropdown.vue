<template>
  <div class="relative">
    <slot name="trigger"
          :toggle="toggle"
          :open="open"
          :close="close"
    />
    <transition name="fade">
      <div v-if="isOpen" v-on-click-outside="close" :class="dropdownClass">
        <div class="py-1 " role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
          <slot />
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref } from 'vue'
import { vOnClickOutside } from '@vueuse/components'

export default {
  name: 'Dropdown',
  directives: {
    onClickOutside: vOnClickOutside
  },
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

    const dropdownRef = ref(null)

    return {
      isOpen,
      open,
      close,
      toggle,
      dropdownRef
    }
  }
}
</script>
