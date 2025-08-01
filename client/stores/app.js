import { defineStore } from "pinia"
import { ref, reactive, computed } from "vue"

export const useAppStore = defineStore("app", () => {
  const crisp = reactive({
    chatOpened: false,
    hidden: false
  })
  const isUnauthorizedError = ref(false)
  const quickLoginModal = ref(false)
  const quickRegisterModal = ref(false)

  const featureBaseEnabled = computed(() => useRuntimeConfig().public.featureBaseOrganization !== null)
  const crispEnabled = computed(() => useRuntimeConfig().public.crispWebsiteId !== null && useRuntimeConfig().public.crispWebsiteId !== '')
  
  // FeatureBase custom button state
  const featureBaseButtonVisible = ref(true)

  // Workspace
  const currentId = useCookie("currentWorkspace")

  const setCurrentId = (id) => {
    currentId.value = id
  }

  // FeatureBase button controls
  const showFeatureBaseButton = () => {
    featureBaseButtonVisible.value = true
  }

  const hideFeatureBaseButton = () => {
    featureBaseButtonVisible.value = false
  }

  return {
    crisp,
    isUnauthorizedError,
    quickLoginModal,
    quickRegisterModal,
    featureBaseEnabled,
    crispEnabled,
  
    // FeatureBase
    featureBaseButtonVisible,
    showFeatureBaseButton,
    hideFeatureBaseButton,

    // Workspace
    currentId,
    setCurrentId,
  }
})
