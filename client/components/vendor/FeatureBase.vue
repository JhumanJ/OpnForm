<template>
  <button
    v-if="user && featureBaseOrganization"
    v-show="scriptLoaded && appStore.featureBaseButtonVisible"
    data-featurebase-feedback
    class="fixed -right-9 top-1/2 -translate-y-1/2 z-20 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded-t-lg shadow-lg transition-all duration-200 hover:shadow-xl transform hover:scale-105 flex items-center space-x-1 -rotate-90 origin-center"
  >
    <Icon name="i-heroicons-chat-bubble-left-ellipsis" class="w-4 h-4" />
    <span class="text-xs">Feedback</span>
  </button>
</template>
<script setup>
import { onMounted } from "vue"
import { default as _has } from "lodash/has"

const scriptLoaded = ref(false)
let setupQueue = []

const authStore = useAuthStore()
const appStore = useAppStore()
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
/* Hide native FeatureBase button if it appears */
.fb-feedback-widget-feedback-button[data-placement="right"],
.fb-feedback-widget-feedback-button[data-placement="left"] {
  display: none !important;
}
</style>
