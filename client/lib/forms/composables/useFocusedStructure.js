import { computed, toValue } from 'vue'

export function useFocusedStructure(formConfig, managerState, formData, fieldState) {
  const form = computed(() => toValue(formConfig) || { properties: [] })

  const isHidden = (field) => {
    try { return !!fieldState.getState(field).hidden } catch { return !!field?.hidden }
  }

  const visibleProperties = computed(() => {
    const properties = form.value.properties || []
    // Focused: one field per page; exclude hidden fields so slides are never empty
    return properties.filter(field => !isHidden(field))
  })

  const fieldGroups = computed(() => {
    const properties = visibleProperties.value || []
    if (properties.length === 0) return [[]]
    return properties.map(field => [field])
  })

  const pageCount = computed(() => {
    const groups = fieldGroups.value
    return groups ? groups.length : 0
  })

  const isLastPage = computed(() => {
    if (managerState?.currentPage === undefined || managerState?.currentPage === null || pageCount.value === undefined) {
      return true
    }
    return managerState.currentPage === pageCount.value - 1
  })

  const getPageFields = (pageIndex) => {
    const groups = fieldGroups.value
    if (!groups) return []
    return groups[pageIndex] || []
  }

  const hasPaymentBlock = (pageIndex) => {
    return getPageFields(pageIndex).some(field => field?.type === 'payment')
  }

  const getPaymentBlock = (pageIndex) => {
    return getPageFields(pageIndex).find(field => field?.type === 'payment')
  }

  const currentPageHasPaymentBlock = computed(() => {
    if (managerState?.currentPage === undefined || managerState?.currentPage === null) return false
    return hasPaymentBlock(managerState.currentPage)
  })

  const currentPagePaymentBlock = computed(() => {
    if (managerState?.currentPage === undefined || managerState?.currentPage === null) return undefined
    return getPaymentBlock(managerState.currentPage)
  })

  // Utilities used by editor/drag in classic; provide no-ops or simple mapping
  const getPageForField = (fieldIndex) => {
    const properties = visibleProperties.value || []
    if (properties.length === 0) return 0
    if (fieldIndex < 0 || fieldIndex >= properties.length) return 0
    return fieldIndex
  }

  const setPageForField = (fieldIndex) => {
    const pageIndex = getPageForField(fieldIndex)
    if (typeof pageIndex === 'number' && !isNaN(pageIndex) && pageIndex >= 0) {
      if (managerState && managerState.currentPage !== undefined) managerState.currentPage = pageIndex
    }
    return pageIndex
  }

  const getTargetDropIndex = (relativeDropIndex, _targetPageIndex) => {
    // In focused mode, the VueDraggable renders ALL form.properties (not filtered)
    // So the indices from Vue Draggable are ALREADY absolute indices!
    // We just need to clamp them to valid range
    const properties = form.value.properties || []
    
    const absoluteIndex = Math.max(0, Math.min(relativeDropIndex, properties.length))
    return absoluteIndex
  }

  const determineInsertIndex = (selectedFieldIndex, currentPageIndex, explicitIndex = null, _insertOnSamePage = false) => {
    if (explicitIndex !== null && typeof explicitIndex === 'number') return explicitIndex
    if (selectedFieldIndex !== null && selectedFieldIndex !== undefined && selectedFieldIndex >= 0) return selectedFieldIndex + 1
    const properties = visibleProperties.value || []
    if (properties.length === 0) return 0
    const pageIdx = currentPageIndex ?? managerState?.currentPage ?? 0
    return Math.max(0, Math.min(properties.length, pageIdx + 1))
  }

  return {
    fieldGroups,
    pageCount,
    isLastPage,
    currentPage: computed(() => managerState?.currentPage ?? 0),
    currentPageHasPaymentBlock,
    currentPagePaymentBlock,
    getPageFields,
    getPageForField,
    hasPaymentBlock,
    getPaymentBlock,
    getTargetDropIndex,
    determineInsertIndex,
    setPageForField
  }
}


