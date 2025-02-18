import { opnFetch } from "./../useOpnApi.js"
import { pendingSubmission as pendingSubmissionFunction } from "./pendingSubmission.js"
import { useWorkingFormStore } from "~/stores/working_form.js"

export const usePartialSubmission = (form, formData) => {
  const pendingSubmission = pendingSubmissionFunction(form)
  const workingFormStore = useWorkingFormStore()

  const SYNC_INTERVAL = 30000 // 30 seconds
  let syncInterval = null
  let syncTimeout = null

  const debouncedSync = () => {
    if (syncTimeout) clearTimeout(syncTimeout)
    syncTimeout = setTimeout(() => {
      syncToServer()
    }, 1000) // 1 second debounce
  }

  const syncToServer = async () => {
    if (!form?.enable_partial_submissions || !formData.value.data() || Object.keys(formData.value.data()).length === 0) return

    try {
      const submissionHash = workingFormStore.getSubmissionHash(pendingSubmission.formPendingSubmissionKey.value)
      const response = await opnFetch(`/forms/${form.slug}/answer`, {
        method: "POST",
        body: {
          ...formData.value.data(),
          'is-partial': true,
          'submission-hash': submissionHash
        }
      })
      if (response.submission_hash) {
        workingFormStore.setSubmissionHash(pendingSubmission.formPendingSubmissionKey.value, response.submission_hash)
      }
    } catch (error) {
      console.error('Failed to sync partial submission', error)
    }
  }

  const startSync = () => {
    if (syncInterval) return

    // Initial sync
    debouncedSync()

    // Regular interval sync
    syncInterval = setInterval(() => {
      debouncedSync()
    }, SYNC_INTERVAL)

    // Sync on visibility/focus changes
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'hidden') {
        debouncedSync()
      }
    })

    window.addEventListener('blur', () => {
      debouncedSync()
    })

    // Sync before page unload
    window.addEventListener('beforeunload', () => {
      // For beforeunload, we want to sync immediately without debounce
      syncToServer()
    })
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
  }

  return {
    startSync,
    stopSync,
    syncToServer
  }
}