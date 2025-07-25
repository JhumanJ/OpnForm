<template>
  <div v-if="isVisible" class="relative group">
    <a class="github-button" href="https://github.com/jhumanj/opnform" data-color-scheme="no-preference: light; light: light; dark: dark;" data-size="large" data-show-count="true" aria-label="Star jhumanj/opnform on GitHub"></a>
    <UButton
      class="absolute -top-2 -right-2 z-10 bg-white dark:bg-gray-800 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
      aria-label="Hide"
      icon="i-heroicons-x-mark"
      variant="soft"
      color="error"
      size="xs"
      @click="hide"
    />
  </div>
</template>

<script setup>
const isVisible = ref(false)
const localStorageKey = 'hide-github-star-button'

onMounted(() => {
  const hideUntil = localStorage.getItem(localStorageKey)
  if (!hideUntil || new Date().getTime() > parseInt(hideUntil, 10)) {
    isVisible.value = true
  }
})

function hide() {
  const hideUntil = new Date().getTime() + 14 * 24 * 60 * 60 * 1000   // 14 days
  localStorage.setItem(localStorageKey, hideUntil.toString())
  isVisible.value = false
}

const scriptTag = computed(() => {
  if (!isVisible.value) {
    return []
  }
  return [
    {
      src: 'https://buttons.github.io/buttons.js',
      async: true,
      defer: true,
    },
  ]
})

useHead({
  script: scriptTag,
})
</script> 