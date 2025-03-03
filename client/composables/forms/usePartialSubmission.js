import { opnFetch } from "./../useOpnApi.js"
import { pendingSubmission as pendingSubmissionFunction } from "./pendingSubmission.js"

// Create a Map to store submission hashes for different forms
const submissionHashes = ref(new Map())

export const usePartialSubmission = (form, formData = {}) => {
  const pendingSubmission = pendingSubmissionFunction(form)

  const SYNC_INTERVAL = 30000 // 30 seconds
  let syncInterval = null
  let syncTimeout = null

  const getSubmissionHash = () => {
    return submissionHashes.value.get(pendingSubmission.formPendingSubmissionKey.value)
  }

  const setSubmissionHash = (hash) => {
    submissionHashes.value.set(pendingSubmission.formPendingSubmissionKey.value, hash)
  }

  const debouncedSync = () => {
    if (syncTimeout) clearTimeout(syncTimeout)
    syncTimeout = setTimeout(() => {
      syncToServer()
    }, 1000) // 1 second debounce
  }

  const syncToServer = async () => {
    if (!form?.enable_partial_submissions || !formData.value.data() || Object.keys(formData.value.data()).length === 0) return

    try {
      const response = await opnFetch(`/forms/${form.slug}/answer`, {
        method: "POST",
        body: {
          ...formData.value.data(),
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
    if (syncInterval) return

    // Initial sync
    debouncedSync()

    // Regular interval sync
    syncInterval = setInterval(() => {
      debouncedSync()
    }, SYNC_INTERVAL)

    // Add event listeners
    document.addEventListener('visibilitychange', handleVisibilityChange)
    window.addEventListener('blur', handleBlur)
    window.addEventListener('beforeunload', handleBeforeUnload)
  }

  const stopSync = () => {
    if (syncInterval) {
      clearInterval(syncInterval)
      syncInterval = null
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

  return {
    startSync,
    stopSync,
    syncToServer,
    getSubmissionHash,
    setSubmissionHash
  }
}