import { opnFetch } from "./../useOpnApi.js"
import { pendingSubmission as pendingSubmissionFunction } from "./pendingSubmission.js"
import { watch, onBeforeUnmount, ref } from 'vue'

// Create a Map to store submission hashes for different forms
const submissionHashes = ref(new Map())

export const usePartialSubmission = (form, formData = {}) => {
  const pendingSubmission = pendingSubmissionFunction(form)

  let syncTimeout = null
  let dataWatcher = null

  const getSubmissionHash = () => {
    return pendingSubmission.getSubmissionHash() ?? submissionHashes.value.get(pendingSubmission.formPendingSubmissionKey.value)
  }

  const setSubmissionHash = (hash) => {
    submissionHashes.value.set(pendingSubmission.formPendingSubmissionKey.value, hash)
    pendingSubmission.setSubmissionHash(hash)
  }

  const debouncedSync = () => {
    if (syncTimeout) clearTimeout(syncTimeout)
    syncTimeout = setTimeout(() => {
      syncToServer()
    }, 1000) // 1 second debounce
  }

  const syncToServer = async () => {
    // Check if partial submissions are enabled and if we have data
    if (!form?.enable_partial_submissions) return
    
    // Get current form data - handle both function and direct object patterns
    const currentData = typeof formData.value?.data === 'function' 
      ? formData.value.data() 
      : formData.value
      
    // Skip if no data or empty data
    if (!currentData || Object.keys(currentData).length === 0) return

    try {
      const response = await opnFetch(`/forms/${form.slug}/answer`, {
        method: "POST",
        body: {
          ...currentData,
          'is_partial': true,
          'submission_hash': getSubmissionHash()
        }
      })
      if (response.submission_hash) {
        setSubmissionHash(response.submission_hash)
      }
    } catch (error) {
      console.error('Failed to sync partial submission', error)
    }
  }

  // Add these handlers as named functions so we can remove them later
  const handleVisibilityChange = () => {
    if (document.visibilityState === 'hidden') {
      debouncedSync()
    }
  }

  const handleBlur = () => {
    debouncedSync()
  }

  const handleBeforeUnload = () => {
    syncToServer()
  }

  const startSync = () => {
    if (dataWatcher) return

    // Initial sync
    debouncedSync()

    // Watch formData directly with Vue's reactivity
    dataWatcher = watch(
      formData,
      () => {
        debouncedSync()
      },
      { deep: true }
    )

    // Add event listeners for critical moments
    document.addEventListener('visibilitychange', handleVisibilityChange)
    window.addEventListener('blur', handleBlur)
    window.addEventListener('beforeunload', handleBeforeUnload)
  }

  const stopSync = () => {
    submissionHashes.value = new Map()

    if (dataWatcher) {
      dataWatcher()
      dataWatcher = null
    }
    
    if (syncTimeout) {
      clearTimeout(syncTimeout)
      syncTimeout = null
    }

    // Remove event listeners
    document.removeEventListener('visibilitychange', handleVisibilityChange)
    window.removeEventListener('blur', handleBlur)
    window.removeEventListener('beforeunload', handleBeforeUnload)
  }

  // Ensure cleanup when component is unmounted
  onBeforeUnmount(() => {
    stopSync()
    
    // Final sync attempt before unmounting
    if(getSubmissionHash()) {
      syncToServer()
    }
  })

  return {
    startSync,
    stopSync,
    syncToServer,
    getSubmissionHash,
    setSubmissionHash
  }
}