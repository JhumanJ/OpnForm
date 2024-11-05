<template>
  <div>
    <Icon
      v-show="isChecked"
      name="i-material-symbols-check-box"
      class="block"
      :class="[
        theme.FlatSelectInput.icon,
        'bg-[var(--form-color,#3B82F6)]'
      ]"
    />
    <Icon
      v-show="!isChecked"
      name="i-material-symbols-check-box-outline-blank"
      class="block"
      :class="[
        theme.FlatSelectInput.icon,
        theme.FlatSelectInput.unselectedIcon,
      ]"
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