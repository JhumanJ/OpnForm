<template>
    <div
      class="border border-nt-blue-light bg-blue-50 dark:bg-notion-dark-light rounded-md p-2 overflow-hidden"
    >
      <div class="flex items-center w-full gap-2">
        <p class="select-all text-nt-blue flex-grow truncate overflow-hidden">
          <a
            v-if="link"
            :href="share_url"
            target="_blank"
          >
            {{ share_url }}
          </a>
          <span v-else>
            {{ share_url }}
          </span>
        </p>
        <UButton
          class="shrink-0"
          size="sm" 
          icon="i-heroicons-clipboard-document"
          label="Copy"
          @click="copyToClipboard"
        />
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, defineProps } from 'vue'
  
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
  
  const { copy } = useClipboard()
  
  const share_url = computed(() => {
    if (props.extraQueryParam) {
        return `${props.form.share_url}?${props.extraQueryParam}`
    }
    return props.form.share_url
  })
  
  function copyToClipboard() {
    if (import.meta.server)
      return
    copy(share_url.value)
    if (props.form.visibility == 'draft') {
      useAlert().warning(
        'Copied! But other people won\'t be able to see the form since it\'s currently in draft mode',
      )
    }
    else {
      useAlert().success('Copied!')
    }
  }
  </script>
  