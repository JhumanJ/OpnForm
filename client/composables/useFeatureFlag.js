import { useFeatureFlags } from './query/useFeatureFlags'

export function useFeatureFlag(flagName, defaultValue = null) {
  const { getFlag } = useFeatureFlags()
  
  return getFlag(flagName, defaultValue)
}