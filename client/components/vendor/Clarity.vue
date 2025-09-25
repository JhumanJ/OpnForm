<template />

<script setup>
import Clarity from '@microsoft/clarity'

const authStore = useAuthStore()
const authenticated = computed(() => authStore.check)
const user = computed(() => authStore.user)
const isIframe = useIsIframe()
const loaded = ref(false)
const runtimeConfig = useRuntimeConfig()
const clarityProjectId = computed(() => runtimeConfig.public.clarityProjectId || null)

async function loadClarity() {
  const projectId = clarityProjectId.value
  if (!projectId || !authenticated.value || isIframe || loaded.value) return

  try {
    Clarity.init(projectId)
    if (user.value?.id) {
      Clarity.identify(String(user.value.id), null, null, user.value.email)
    }
    loaded.value = true
  } catch (error) {
    console.error('[Clarity] Failed to init', error)
  }
}

watch(authenticated, (val) => {
  if (val) loadClarity()
})

watch(user, (val) => {
  if (import.meta.server) return
  if (!loaded.value || !authenticated.value || isIframe) return
  if (val?.id) {
    try {
      Clarity.identify(String(val.id), null, null, val.email)
    } catch (error) {
      console.error('[Clarity] Failed to identify user', error)
    }
  }
})

onMounted(() => {
  if (import.meta.server) return
  loadClarity()
})
</script>


