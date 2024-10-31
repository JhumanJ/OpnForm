<template>
  <div>
    <Icon
      v-show="isChecked"
      name="ic:round-radio-button-checked"
      :class="[
        theme.FlatSelectInput.icon
      ]"
      :color="color"
    />
    <Icon
      v-show="!isChecked"
      name="ic:round-radio-button-unchecked"
      :class="[
        theme.FlatSelectInput.icon,
        theme.FlatSelectInput.unselectedIcon,
      ]"
      :color="undefined"
    />
  </div>
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