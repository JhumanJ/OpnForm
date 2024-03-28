
let darkModeNodeParent = import.meta.client ? document.body : null

/**
 * Handle form public pages dark mode and transparent mode
 */
export function handleDarkMode (darkMode, elem = null) {
  if (import.meta.server) return

  darkModeNodeParent = elem ?? document.body

  // Dark mode
  if (['dark', 'light'].includes(darkMode)) {
    return handleDarkModeToggle(darkMode === 'dark')
  }

  // Case auto
  handleDarkModeToggle(window.matchMedia('(prefers-color-scheme: dark)').matches)

  // Create listener
  window.matchMedia('(prefers-color-scheme: dark)')
    .addEventListener('change', handleDarkModeToggle)
}

export function darkModeEnabled() {
  if (import.meta.server) return false
  return computed(() => document.body.classList.contains('dark'))
}

function handleDarkModeToggle (enabled) {
  if (enabled !== false && enabled !== true) {
    // if we received an event
    enabled = enabled.matches
  }
  enabled ? darkModeNodeParent.classList.add('dark') : darkModeNodeParent.classList.remove('dark')
}

export function disableDarkMode () {
  if (import.meta.server) return
  const body = document.body
  body.classList.remove('dark')
  // Remove event listener
  window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', handleDarkModeToggle)
}

export function handleTransparentMode (transparentModeEnabled) {
  if (import.meta.server) return
  if (!useIsIframe() || !transparentModeEnabled) return

  const app = document.getElementById('app')
  app.classList.remove('bg-white')
  app.classList.remove('dark:bg-notion-dark')
  app.classList.add('bg-transparent')
}

export function focusOnFirstFormElement() {
  if (import.meta.server) return
  for (const ele of document.querySelectorAll('input,button,textarea,[role="button"]')) {
    if (ele.offsetWidth !== 0 || ele.offsetHeight !== 0) {
      ele.focus()
      break
    }
  }
}
