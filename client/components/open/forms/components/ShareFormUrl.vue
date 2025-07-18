<template>
  <CopyContent
    :content="share_url"
    label="Copy"
    class="w-auto"
  />
</template>
  
<script setup>
import { computed, defineProps } from 'vue'
import CopyContent from './CopyContent.vue'

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  link: {
    type: Boolean,
    default: false,
  },
  extraQueryParam: {
    type: String,
    default: '',
  },
})

const share_url = computed(() => {
  if (props.extraQueryParam) {
      return `${props.form.share_url}?${props.extraQueryParam}`
  }
  return props.form.share_url
})
</script>
  