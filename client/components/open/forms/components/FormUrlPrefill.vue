<template>
  <div
    class="border border-nt-blue-light bg-blue-50 dark:bg-notion-dark-light shadow rounded-md p-4 mb-5 w-full mx-auto mt-4 select-all"
  >
    <div class="flex items-center">
      <p class="select-all flex-grow break-all" v-html="preFillUrl" />
      <div class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer" @click="copyToClipboard">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
             stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"
          />
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue'
import { default as _has } from 'lodash/has'
const { copy } = useClipboard()

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  formData: {
    type: Object,
    required: true
  },
  extraQueryParam: {
    type: String,
    default: ''
  }
})

const preFillUrl = computed(() => {
  const url = props.form.share_url
  const uriComponents = new URLSearchParams()
  props.form.properties.filter((property) => {
    return _has(props.formData, property.id) && props.formData[property.id] !== null
  }).forEach((property) => {
    if (Array.isArray(props.formData[property.id])) {
      props.formData[property.id].forEach((value) => {
        uriComponents.append(property.id + '[]', value)
      })
    } else {
      uriComponents.append(property.id, props.formData[property.id])
    }
  })

  if(uriComponents.toString() !== ""){
    return (props.extraQueryParam) ? url + '?' + uriComponents + '&' + props.extraQueryParam : url + '?' + uriComponents
  }else{
    return (props.extraQueryParam) ? url + '?' + props.extraQueryParam : url
  }
})

const copyToClipboard = () => {
  if (import.meta.server) return
  copy(preFillUrl.value)
  useAlert().success('Copied!')
}
</script>
