<template>
  <span class="text-sm text-neutral-900" v-if="valueIsObject">
    <template v-if="value[0]">{{ formattedDate(value[0]) }}</template>
    <template v-if="value[1]"><b class="mx-2">to</b>{{ formattedDate(value[1]) }}</template>
  </span>
  <span class="text-sm text-neutral-900" v-else>
    {{ formattedDate(value) }}
  </span>
</template>

<script setup>
import { format } from 'date-fns'
import { default as _has } from 'lodash/has'

const props = defineProps({
  property: {
    required: true
  },
  value: {
    required: true
  }
})

const valueIsObject = computed(() => {
  if (typeof props.value === 'object' && props.value !== null) {
    return true
  }
  return false
})

const formattedDate = (val) => {
  if (!val) return ''
  const dateFormat = _has(props.property, 'date_format') ? props.property.date_format : 'dd/MM/yyyy'
  const timeFormat = _has(props.property, 'time_format') ? props.property.time_format : '24'
  if (props.property?.with_time) {
    try {
      return format(new Date(val), dateFormat + (timeFormat == 12 ? ' p':' HH:mm'))
    } catch {
      return ''
    }
  }
  try {
    return format(new Date(val), dateFormat)
  } catch {
    return ''
  }
}
</script>
