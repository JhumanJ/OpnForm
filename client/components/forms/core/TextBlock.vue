<template>
  <div
    class="break-words whitespace-break-spaces"
    v-html="processedContent"
  />
</template>
  
  <script setup>
  import { useParseMention } from '@/composables/components/useParseMention'
  
  const props = defineProps({
    content: {
      type: String,
      required: true
    },
    mentionsAllowed: {
      type: Boolean,
      default: false
    },
    form: {
      type: Object,
      default: null
    },
    formData: {
      type: Object,
      default: null
    }
  })
  const processedContent = computed(() => {
    return useParseMention(props.content, props.mentionsAllowed, props.form, props.formData)
  })
  </script>