import { defineStore } from "pinia"
import { ref, reactive, computed } from "vue"

export const useAppStore = defineStore("app", () => {
  const navbarHidden = ref(false)
  const crisp = reactive({
    chatOpened: false,
    hidden: false
  })
  const isUnauthorizedError = ref(false)
  const quickLoginModal = ref(false)
  const quickRegisterModal = ref(false)

  const featureBaseEnabled = computed(() => useRuntimeConfig().public.featureBaseOrganization !== null)
  const crispEnabled = computed(() => useRuntimeConfig().public.crispWebsiteId !== null && useRuntimeConfig().public.crispWebsiteId !== '')

  const hideNavbar = () => {
    navbarHidden.value = true
  }

  const showNavbar = () => {
    navbarHidden.value = false
  }

  // Workspace
  const currentId = useCookie("currentWorkspace")

  const setCurrentId = (id) => {
    currentId.value = id
  }

  return {
    navbarHidden,
    crisp,
    isUnauthorizedError,
    quickLoginModal,
    quickRegisterModal,
    featureBaseEnabled,
    crispEnabled,
    hideNavbar,
    showNavbar,

    // Workspace
    currentId,
    setCurrentId,
  }
})
