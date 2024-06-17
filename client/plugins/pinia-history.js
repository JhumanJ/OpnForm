import { computed, reactive } from 'vue'
import { compress, decompress } from 'lzutf8'
import debounce from 'debounce'

/**
 * Merges the user-provided options with the default plugin options.
 * @param {boolean|object} options - The user's configuration options.
 * @returns {object} The merged configuration options.
 */
function mergeOptions(options) {
  const defaults = {
    max: 10,
    persistent: false,
    persistentStrategy: {
      get: function(store, type) {
        if (typeof localStorage !== 'undefined') {
          const key = `pinia-history-${store.$id}-${type}`
          const value = localStorage.getItem(key)
          return value ? decompress(value, { inputEncoding: 'Base64' }).split(',') : undefined
        }
      },
      set: function(store, type, value) {
        if (typeof localStorage !== 'undefined') {
          const key = `pinia-history-${store.$id}-${type}`
          const string = compress(value.join(','), { outputEncoding: 'Base64' })
          localStorage.setItem(key, string)
        }
      },
      remove: function(store, type) {
        if (typeof localStorage !== 'undefined') {
          const key = `pinia-history-${store.$id}-${type}`
          localStorage.removeItem(key)
        }
      }
    }
  }

  return {
    ...defaults,
    ...(typeof options === 'boolean' ? {} : options)
  }
}

/**
 * Adds undo/redo functionality to a Pinia store.
 * @param {PiniaPluginContext} context - The context provided by Pinia.
 */
const PiniaHistory = (context) => {
  const { store, options } = context
  const { history } = options

  if (history) {
    const mergedOptions = mergeOptions(history)
    const { max, persistent, persistentStrategy } = mergedOptions

    const $history = reactive({
      max,
      persistent,
      persistentStrategy,
      done: [],
      undone: [],
      current: JSON.stringify(store.$state),
      trigger: true,
      type: null
    })

    const debouncedStoreUpdate = debounce((state) => {
      if ($history.trigger) {
        if ($history.done.length >= max) $history.done.shift()
        if ($history.type === null) {
          $history.done.push($history.current)
          $history.undone = [] // Clear redo history on new action
        }
        $history.current = JSON.stringify(state)
        if (persistent) {
          persistentStrategy.set(store, 'undo', $history.done)
          persistentStrategy.set(store, 'redo', $history.undone)
        }
      }
    }, 300) // Adjust debounce delay as needed

    store.canUndo = computed(() => $history.done.length > 0)
    store.canRedo = computed(() => $history.undone.length > 0)

    store.undo = () => {
      if (store.canUndo) {
        const state = $history.done.pop()
        if (state !== undefined) {
          $history.undone.push($history.current) // Save current state for redo
          $history.type = 'undo'
          $history.trigger = false
          store.$patch(JSON.parse(state))
          $history.current = state
          $history.trigger = true
          if (persistent) {
            persistentStrategy.set(store, 'undo', $history.done)
            persistentStrategy.set(store, 'redo', $history.undone)
          }
        }
      }
    }

    store.redo = () => {
      if (store.canRedo) {
        const state = $history.undone.pop()
        if (state !== undefined) {
          $history.done.push($history.current) // Save current state for undo
          $history.type = 'redo'
          $history.trigger = false
          store.$patch(JSON.parse(state))
          $history.current = state
          $history.trigger = true
          if (persistent) {
            persistentStrategy.set(store, 'undo', $history.done)
            persistentStrategy.set(store, 'redo', $history.undone)
          }
        }
      }
    }

    store.clear = () => {
      $history.done = []
      $history.undone = []
      if (persistent) {
        persistentStrategy.set(store, 'undo', $history.done)
        persistentStrategy.set(store, 'redo', $history.undone)
      }
    }

    store.$subscribe((mutation, state) => {
      debouncedStoreUpdate(state)
    })
  }
}

export default defineNuxtPlugin(nuxtApp => {
  nuxtApp.$pinia.use(PiniaHistory)
})
