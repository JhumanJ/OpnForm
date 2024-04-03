<template>
  <div :class="classes">
    <Icon v-if="beforeIcon" :name="beforeIcon" :class="iconClasses"/>
    <slot></slot>
    <Icon v-if="afterIcon" :name="afterIcon" :class="iconClasses"/>
  </div>
</template>

<script setup>
import { default as _has } from 'lodash/has'

const props = defineProps({
  color: {
    type: String,
    default: 'green'
  },
  beforeIcon: {
    type: String,
    default: null
  },
  afterIcon: {
    type: String,
    default: null
  }
})

const baseClasses = {
  'green': ['bg-green-100', 'border', 'border-green-300', 'text-green-700'],
  'red': ['bg-red-100', 'border', 'border-red-300', 'text-red-700'],
  'gray': ['bg-gray-100', 'border', 'border-gray-300', 'text-gray-700'],
}

const iconBaseClasses = {
  'green': ['text-green-500'],
  'red': ['text-red-500'],
  'gray': ['text-gray-500'],
}

const activeColor = computed(() => {
  return _has(baseClasses, props.color) ? props.color : 'gray'
})

const classes = computed(() => {
  const classes = ['border', 'text-xs', 'px-2', 'inline-flex', 'items-center', 'rounded-full'].concat(baseClasses[activeColor.value])
  return classes.join(' ')
})

const iconClasses = computed(() => {
  return iconBaseClasses[activeColor.value].concat(['w-2 h-2 mr-1']).join(' ')
})
</script>
