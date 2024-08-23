<template>
  <Icon
    :name="isChecked ? 'material-symbols:check-box' : 'material-symbols:check-box-outline-blank'"
    :class="[
      theme.FlatSelectInput.icon,
      isChecked ? '' : theme.FlatSelectInput.unselectedIcon,
    ]"
    :color="isChecked ? color : undefined"
  />
</template>

<script setup>
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