<template>
  <ClientOnly>
    <div
      ref="dropdown"
      class="relative"
    >
      <slot
        name="trigger"
        :toggle="toggle"
        :open="open"
        :close="close"
      />

      <Collapsible
        v-model="isOpen"
        :class="[dropdownClass, dropdownBgClass]"
        @click-away="onClickAway"
      >
        <div
          class="py-1"
          role="menu"
          aria-orientation="vertical"
          aria-labelledby="options-menu"
        >
          <slot />
        </div>
      </Collapsible>
    </div>
  </ClientOnly>
</template>

<script setup>
import { ref } from 'vue'
import Collapsible from './transitions/Collapsible.vue'

defineProps({
  dropdownClass: {
    type: String,
    default:
      'origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 z-20'
  },
  dropdownBgClass: {
    type: String,
    default: 'bg-white dark:bg-gray-800'
  }
})

const isOpen = ref(false)
const dropdown = ref(null)

function open () {
  isOpen.value = true
}

function close () {
  isOpen.value = false
}

function toggle () {
  isOpen.value = !isOpen.value
}

function onClickAway (event) {
  // Check that event target isn't children of dropdown
  if (dropdown.value && !dropdown.value.contains(event.target))
    close(event)
}

defineExpose({
  open,
  close,
  toggle
})
</script>
