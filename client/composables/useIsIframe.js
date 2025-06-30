export const useIsIframe = () => {
  if (import.meta.client) {
    return window.location !== window.parent.location || (window.frameElement !== null)
  }
  return false
}
