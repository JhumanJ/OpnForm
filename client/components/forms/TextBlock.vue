<template>
    <div
      v-html="processedContent"
    />
  </template>
  
  <script setup>
  import { FormSubmissionFormatter } from './components/FormSubmissionFormatter'
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
    const form = props.form
    const formData = props.formData
    if (!props.mentionsAllowed || !form || !formData) {
      return props.content
    }
    const formatter = new FormSubmissionFormatter(form, formData).setOutputStringsOnly()
    const formattedData = formatter.getFormattedData()
    console.trace(props.content);
    return props.content.replace(/<span[^>]*mention-field-id="([^"]*)"[^>]*mention-fallback="([^"]*)"[^>]*>.*?<\/span>/g, (match, fieldId, fallback) => {
      const value = formattedData[fieldId]
      if (value) {
        if(Array.isArray(value)) {
          return value.map(v => `<span>${v}</span>`).join(' ')
        }
        return `<span>${value}</span>`
      } else if (fallback) {
        return `<span>${fallback}</span>`
      }
      return ''
    })
  })
  </script>