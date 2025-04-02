import {computed, reactive} from 'vue'
import debounce from 'debounce'
import {hash} from "~/lib/utils.js"

/**
 * Merges the user-provided options with the default plugin options.
 * @param {boolean|object} options - The user's configuration options.
 * @returns {object} The merged configuration options.
 */
function mergeOptions(options) {
  const defaults = {
    max: 30,
    persistent: false,
    persistentStrategy: {
      get: function (_store, _type) {
        // Todo
      },
      set: function (_store, _type, _value) {
        // Todo
      },
      remove: function (store, type) {
        if (typeof localStorage !== 'undefined') {
          const key = `pinia-history-${store.$id}-${type}`
          localStorage.removeItem(key)
        }
      }
    },
    debounceWait: 300
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
  const {store, options} = context
  const {history} = options

  if (!history) {
    return
  }
  const mergedOptions = mergeOptions(history)
  const {max, persistent, persistentStrategy} = mergedOptions

  const $history = reactive({
    max,
    persistent,
    persistentStrategy,
    done: [],
    undone: [],
    current: JSON.stringify(store.$state),
    trigger: true,
  })

  const debouncedStoreUpdate = debounce((state) => {
    if (hash($history.current) === hash(JSON.stringify(state))) { // Not a real change here
      return
    }
    if ($history.done.length >= max) $history.done.shift() // Remove oldest state if needed

    $history.done.push($history.current)
    $history.undone = [] // Clear redo history on new action
    $history.current = JSON.stringify(state)

    if (persistent) {
      persistentStrategy.set(store, 'undo', $history.done)
      persistentStrategy.set(store, 'redo', $history.undone)
    }
  }, mergedOptions.debounceWait)

  store.canUndo = computed(() => $history.done.length > 0)
  store.canRedo = computed(() => $history.undone.length > 0)

  store.undo = () => {
    if (!store.canUndo) {
      return
    }

    debouncedStoreUpdate.clear()
    const state = $history.done.pop()
    if (state === undefined) {
      return
    }

    $history.undone.push($history.current) // Save current state for redo
    $history.trigger = false
    store.$patch(JSON.parse(state))
    nextTick(() => {
      $history.current = state
      $history.trigger = true
      if (persistent) {
        persistentStrategy.set(store, 'undo', $history.done)
        persistentStrategy.set(store, 'redo', $history.undone)
      }
    })

  }

  store.redo = () => {
    if (!store.canRedo) {
      return
    }
    debouncedStoreUpdate.clear()
    const state = $history.undone.pop()
    if (state === undefined) {
      return
    }

    $history.done.push($history.current) // Save current state for undo
    $history.trigger = false
    store.$patch(JSON.parse(state))
    nextTick(() => {
      $history.current = state
      $history.trigger = true
      if (persistent) {
        persistentStrategy.set(store, 'undo', $history.done)
        persistentStrategy.set(store, 'redo', $history.undone)
      }
    })
  }

  store.clearHistory = () => {
    $history.done = []
    $history.undone = []
    if (persistent) {
      persistentStrategy.set(store, 'undo', $history.done)
      persistentStrategy.set(store, 'redo', $history.undone)
    }
  }

  store.$subscribe((mutation, state) => {
    if ($history.trigger) {
      debouncedStoreUpdate(state)
    }
  })

}

export default defineNuxtPlugin(nuxtApp => {
  nuxtApp.$pinia.use(PiniaHistory)
})
