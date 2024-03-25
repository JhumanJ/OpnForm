<template>
  <div class="relative" ref="dropdown">
    <slot name="trigger"
          :toggle="toggle"
          :open="open"
          :close="close"
    />

    <collapsible v-model="isOpen" :class="dropdownClass" @click-away="onClickAway">
      <div class="py-1 " role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <slot/>
      </div>
    </collapsible>
  </div>
</template>

<script setup>
import {ref} from 'vue'
import Collapsible from './transitions/Collapsible.vue'

const props = defineProps({
  dropdownClass: {
    type: String,
    default: 'origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20'
  }
})

const isOpen = ref(false)
const dropdown = ref(null)

const open = (event) => {
  isOpen.value = true
}

const close = (event) => {
  isOpen.value = false
}

const toggle = (event) => {
  isOpen.value = !isOpen.value
}

const onClickAway = (event) => {
  // Check that event target isn't children of dropdown
  if (dropdown.value && !dropdown.value.contains(event.target)) {
    close(event)
  }
}

defineExpose({
  open,
  close,
  toggle
})
</script>
