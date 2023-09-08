<template>
  <div class="relative">
    <slot name="trigger"
          :toggle="toggle"
          :open="open"
          :close="close"
    />
    <transition name="fade">
      <div
        v-if="isOpen"
        v-on-clickaway="close"
        :class="dropdownClass"
      >
        <div class="py-1 " role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
          <slot/>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import {directive as onClickaway} from 'vue-clickaway'

export default {
  name: 'Dropdown',
  directives: {
    onClickaway: onClickaway
  },

  props: {
    dropdownClass: {
      type: String,
      default: 'origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20'
    }
  },
  data() {
    return {
      isOpen: false
    }
  },
  methods: {
    open() {
      this.isOpen = true
    },
    close() {
      this.isOpen = false
    },
    toggle() {
      this.isOpen = !this.isOpen
    }
  }
}
</script>
