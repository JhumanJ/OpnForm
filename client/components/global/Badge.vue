<template>
  <div :class="classes">
    <slot name="before"/>
    <slot></slot>
    <slot name="after"/>
  </div>
</template>

<script setup>
const props = defineProps({
  color: {
    type: String,
    default: 'green'
  }
})

const baseClasses = {
  'green': ['bg-green-100', 'border', 'border-green-300', 'text-green-700'],
  'red': ['bg-red-100', 'border', 'border-red-300', 'text-red-700'],
  'gray': ['bg-gray-100', 'border', 'border-gray-300', 'text-gray-700'],
}

const classes = computed(() => {
  const activeColor = Object.hasOwn(baseClasses, props.color) ? props.color : 'green'
  const classes = ['border', 'text-xs', 'px-2', 'inline-flex', 'items-center', 'rounded-full'].concat(baseClasses[activeColor])
  return classes.join(' ')
})
</script>
