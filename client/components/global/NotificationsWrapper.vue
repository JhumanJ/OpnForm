<template>
  <UNotifications
    :ui="{
      strategy: 'override',
      position,
    }"
  >
    <template #title="{ title }">
      <span v-html="title" />
    </template>

    <template #description="{ description }">
      <span v-html="description" />
    </template>
  </UNotifications>
</template>

<script setup>
// fixed flex flex-col justify-end z-[55] bottom-0 end-0 w-full sm:w-96
// fixed flex flex-col justify-end z-[55] top-auto bottom-0 w-full sm:w-96
const appStore = useAppStore()
const crispChatOpened = computed(() => appStore.crisp.chatOpened)
const crispHidden = computed(() => appStore.crisp.hidden)

const position = computed(() => {
  if (crispHidden.value) {
    return 'end-0 bottom-0'
  } else {
    if (crispChatOpened.value) {
      return 'bottom-0'
    }
    return 'end-0 bottom-20'
  }
})
</script>
