<template></template>
<script setup>
import { onMounted } from "vue"
import { default as _has } from "lodash/has"

const scriptLoaded = ref(false)
let setupQueue = []

const authStore = useAuthStore()
const { data: user } = useAuth().user()
const isImpersonating = computed(() => authStore.isImpersonating)
const featureBaseOrganization =
  useRuntimeConfig().public.featureBaseOrganization

const runSetupQueue = () => {
  while (setupQueue.length > 0) {
    const setupFn = setupQueue.shift()
    try {
      setupFn()
    } catch (error) {
      console.error("Error running FeatureBase setup", error)
    }
  }
}

const loadScript = () => {
  if (scriptLoaded.value || !user.value || !featureBaseOrganization) return
  if (document.getElementById("featurebase-sdk")) return

  const script = document.createElement("script")
  script.src = "https://do.featurebase.app/js/sdk.js"
  script.id = "featurebase-sdk"
  document.head.appendChild(script)
  script.onload = () => {
    scriptLoaded.value = true
    runSetupQueue()
  }
}

const setupForUser = () => {
  if (
    import.meta.server ||
    !user.value ||
    !featureBaseOrganization ||
    isImpersonating.value
  ) {
    return
  }

  window.Featurebase("identify", {
    organization: featureBaseOrganization,
    email: user.value.email,
    name: user.value.name,
    id: user.value.id != null ? String(user.value.id) : undefined,
    profilePicture: user.value.photo_url,
  })

  window.Featurebase("initialize_changelog_widget", {
    organization: featureBaseOrganization,
    placement: "right",
    theme: "light",
    alwaysShow: true,
    fullscreenPopup: true,
    usersName: user.value?.name,
  })

  window.Featurebase("initialize_feedback_widget", {
    organization: featureBaseOrganization,
    theme: "light",
    placement: "right",
    email: user.value?.email,
    usersName: user.value?.name,
  })
}

const scheduleSetup = () => {
  if (import.meta.server) return

  setupQueue.push(setupForUser)

  if (scriptLoaded.value) {
    runSetupQueue()
  } else {
    loadScript()
  }
}

onMounted(() => {
  if (import.meta.server || !user.value) return

  // Setup base
  if (
    !_has(window, "Featurebase") ||
    typeof window.Featurebase !== "function"
  ) {
    window.Featurebase = function () {
      ;(window.Featurebase.q = window.Featurebase.q || []).push(arguments)
    }
  }

  if (user.value) {
    scheduleSetup()
  }
})

watch(
  user,
  (val) => {
    if (val) {
      scheduleSetup()
    }
  },
  { immediate: false }
)
</script>

<style>
.fb-feedback-widget-feedback-button {
  z-index: 20 !important;
}
</style>
