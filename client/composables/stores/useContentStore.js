
// Composable with all the logic to encapsulate a default content store

export const useContentStore = (mapKey = 'id') => {

  const content = ref(new Map())
  const loading = ref(false)

  // Computed
  const getAll = computed(() => {
    return [...content.value.values()]
  })
  const getByKey = (key) => {
    if (Array.isArray(key)) {
      return key.map((k) => content.value.get(k)).filter((item) => item !== undefined)
    }
    return content.value.get(key)
  }

  // Actions
  function set(items) {
    content.value = new Map
    items.forEach((item) => {
      content.value.set(item[mapKey], item)
    })
  }

  function save(items) {
    if (!Array.isArray(items)) items = [items]
    items.forEach((item) => {
      content.value.set(item[mapKey], item)
    })
  }

  function remove(item) {
    content.value.remove(item[mapKey])
  }

  function startLoading() {
    loading.value = true
  }

  function stopLoading() {
    loading.value = false
  }

  function resetState() {
    set([])
    stopLoading()
  }

  return {
    content,
    loading,
    getAll,
    getByKey,
    set,
    save,
    remove,
    startLoading,
    stopLoading,
    resetState
  }
}
