import { useFeatureFlagsStore } from '~/stores/featureFlags'

export default defineNuxtPlugin((nuxtApp) => {
  const featureFlagsStore = useFeatureFlagsStore()

  nuxtApp.provide('featureFlag', (key, defaultValue = false) => {
    return featureFlagsStore.getFlag(key, defaultValue)
  })
})