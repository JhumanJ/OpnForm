<template>
  <Icon
    :name="isChecked ? 'ic:round-radio-button-checked' : 'ic:round-radio-button-unchecked'"
    :class="[
      theme.FlatSelectInput.icon,
      isChecked ? '' : theme.FlatSelectInput.unselectedIcon,
    ]"
    :color="isChecked ? color : undefined"
  />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  isChecked: {
    type: Boolean,
    required: true
  },
  color: {
    type: String,
    default: ''
  },
  theme: {
    type: Object,
    required: true
  }
})
</script>