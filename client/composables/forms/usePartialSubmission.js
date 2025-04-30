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
    // Clear existing timeout to reset the timer
    if (syncTimeout) {
      clearTimeout(syncTimeout);
    }
    
    // Set a new timeout - increased to 2 seconds for less frequent syncing
    syncTimeout = setTimeout(() => {
      syncToServer();
    }, 2000); // 2 second debounce
  }

  // Add a function to execute sync immediately without debouncing
  // This is used for critical moments like page unload
  const syncImmediately = () => {
    if (syncTimeout) {
      clearTimeout(syncTimeout);
      syncTimeout = null;
    }
    return syncToServer();
  }

  const syncToServer = async () => {
    // Check if partial submissions are enabled and if we have data
    if (!form?.enable_partial_submissions) {
      return;
    }
    
    // Get current form data - now handling the computed ref pattern
    const currentData = typeof formData === 'function' 
      ? formData() 
      : formData.value;
      
    // Skip if no data or empty data
    if (!currentData || Object.keys(currentData).length === 0) {
      return;
    }

    try {
      const response = await opnFetch(`/forms/${form.slug}/answer`, {
        method: "POST",
        body: {
          ...currentData,
          'is_partial': true,
          'submission_hash': getSubmissionHash()
        }
      });
      if (response.submission_hash) {
        setSubmissionHash(response.submission_hash);
      }
    } catch (error) {
      // Error handling without console.log
    }
  }

  // Add these handlers as named functions so we can remove them later
  const handleVisibilityChange = () => {
    if (document.visibilityState === 'hidden') {
      // When tab becomes hidden, sync immediately
      syncImmediately();
    }
  }

  const handleBlur = () => {
    // When window loses focus, sync immediately
    syncImmediately();
  }

  const handleBeforeUnload = () => {
    // Before page unloads, sync immediately
    syncImmediately();
  }

  const startSync = () => {
    if (dataWatcher) {
      return;
    }

    // Initial sync
    debouncedSync();

    // Watch formData using Vue's reactivity
    dataWatcher = watch(
      formData,
      (newValue) => {
        debouncedSync();
      },
      { deep: true }
    );

    // Add event listeners for critical moments
    document.addEventListener('visibilitychange', handleVisibilityChange);
    window.addEventListener('blur', handleBlur);
    window.addEventListener('beforeunload', handleBeforeUnload);
  }

  const stopSync = () => {
    // Final sync before stopping if we have a hash
    if (getSubmissionHash()) {
      syncImmediately();
    }
    
    submissionHashes.value = new Map();

    if (dataWatcher) {
      dataWatcher();
      dataWatcher = null;
    }
    
    if (syncTimeout) {
      clearTimeout(syncTimeout);
      syncTimeout = null;
    }

    // Remove event listeners
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    window.removeEventListener('blur', handleBlur);
    window.removeEventListener('beforeunload', handleBeforeUnload);
  }

  // Ensure cleanup when component is unmounted
  onBeforeUnmount(() => {
    stopSync();
  });

  return {
    startSync,
    stopSync,
    syncToServer: debouncedSync, // Expose the debounced version externally
    syncImmediately, // Also expose the immediate sync for critical situations
    getSubmissionHash,
    setSubmissionHash
  }
}