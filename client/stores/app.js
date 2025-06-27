import { defineStore } from "pinia"
import { nextTick } from "vue"

export const useAppStore = defineStore("app", {
  state: () => ({
    layout: "default",
    navbarHidden: false,
    crisp: {
      chatOpened: false,
      hidden: false
    },
    isUnauthorizedError: false,
    quickLoginModal: false,
    quickRegisterModal: false,
    
    // User Settings Modal
    userSettingsModal: {
      open: false,
      activeTab: 'account' // Default to first tab instead of null
    },

    // Workspace Settings Modal
    workspaceSettingsModal: {
      open: false,
      activeTab: 'information' // Default to first tab instead of null
    },
    
    // App Loader
    loader: {
      percent: 0,
      show: false,
      canSuccess: true,
      duration: 3000,
      _timer: null,
      _cut: null,
    },
  }),
  getters: {
    featureBaseEnabled: () => useRuntimeConfig().public.featureBaseOrganization !== null,
    crispEnabled: () => useRuntimeConfig().public.crispWebsiteId !== null && useRuntimeConfig().public.crispWebsiteId !== '',
  },
  actions: {
    hideNavbar() {
      this.navbarHidden = true
    },
    showNavbar() {
      this.navbarHidden = false
    },
    setLayout(layout) {
      this.layout = layout ?? "default"
    },
    loaderIncrease(num) {
      this.loader.percent = this.loader.percent + Math.floor(num)
    },
    loaderDecrease(num) {
      this.loader.percent = this.loader.percent - Math.floor(num)
    },
    loaderFinish() {
      this.loader.percent = 100
      this.loaderHide()
    },
    loaderSetTimer(timerVal) {
      this.loader._timer = timerVal
    },
    loaderPause() {
      clearInterval(this.loader._timer)
    },
    loaderHide() {
      this.loaderPause()
      this.loader._timer = null
      setTimeout(() => {
        this.loader.show = false
        nextTick(() => {
          setTimeout(() => {
            this.loader.percent = 0
          }, 200)
        })
      }, 500)
    },
    loaderFail() {
      this.loader.canSuccess = false
    },
    loaderStart() {
      this.loader.show = true
      this.loader.canSuccess = true
      if (this.loader._timer) {
        clearInterval(this.loader._timer)
        this.loader.percent = 0
      }
      this.loader._cut = 10000 / Math.floor(this.loader.duration)

      this.loaderSetTimer(
        setInterval(() => {
          this.loaderIncrease(this.loader._cut * Math.random())
          if (this.loader.percent > 95) {
            this.loaderFinish()
          }
        }, 100),
      )
    },
    
    // User Settings Modal methods
    openUserSettingsModal(activeTab = 'account') {
      this.userSettingsModal.open = true
      this.userSettingsModal.activeTab = activeTab
    },
    closeUserSettingsModal() {
      this.userSettingsModal.open = false
      // Don't reset activeTab immediately - let the modal component handle it
    },
    setUserSettingsActiveTab(activeTab) {
      this.userSettingsModal.activeTab = activeTab
    },

    // Workspace Settings Modal methods
    openWorkspaceSettingsModal(activeTab = 'information') {
      this.workspaceSettingsModal.open = true
      this.workspaceSettingsModal.activeTab = activeTab
    },
    closeWorkspaceSettingsModal() {
      this.workspaceSettingsModal.open = false
      // Don't reset activeTab immediately - let the modal component handle it
    },
    setWorkspaceSettingsActiveTab(activeTab) {
      this.workspaceSettingsModal.activeTab = activeTab
    },
  },
})
