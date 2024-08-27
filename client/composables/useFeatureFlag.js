
export function useFeatureFlag(flagName, defaultValue = null) {
  const featureStore = useFeatureFlagsStore()
  return featureStore.getFlag(flagName, defaultValue)
}