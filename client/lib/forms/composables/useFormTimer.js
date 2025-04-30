import { ref } from 'vue';

/**
 * @fileoverview Composable for managing form completion time.
 * Includes persistence in localStorage via pendingSubmission.
 */
export function useFormTimer(formConfig, pendingSubmission) {
  // Reactive state for timer
  const startTime = ref(null);
  const completionTime = ref(null);
  const isTimerActive = ref(false);
  const isInitialized = ref(false);

  /**
   * Loads timer data from localStorage if available.
   * @returns {Boolean} Whether data was loaded successfully
   */
  const loadFromLocalStorage = () => {
    if (import.meta.server || !pendingSubmission) return false;
    
    const savedTimer = pendingSubmission.getTimer();
    if (savedTimer) {
      startTime.value = parseInt(savedTimer);
      console.log(`Loaded timer from localStorage: ${startTime.value}`);
      return true;
    }
    return false;
  };

  /**
   * Saves the current start time to localStorage.
   */
  const saveToLocalStorage = () => {
    if (import.meta.server || !pendingSubmission) return;
    
    if (startTime.value) {
      pendingSubmission.setTimer(startTime.value.toString());
      console.log(`Saved timer to localStorage: ${startTime.value}`);
    } else {
      pendingSubmission.removeTimer();
      console.log('Removed timer from localStorage');
    }
  };

  /**
   * Starts the timer if not already active.
   * Will load from localStorage on first call if available.
   */
  const start = () => {
    if (isTimerActive.value) {
      console.log('Timer already active.');
      return;
    }
    
    // If this is the first time starting, try to load from localStorage
    if (!isInitialized.value) {
      isInitialized.value = true;
      loadFromLocalStorage();
    }
    
    // Only set a new start time if one doesn't exist already
    if (!startTime.value) {
      startTime.value = Date.now();
    }
    
    isTimerActive.value = true;
    saveToLocalStorage();
    console.log('Form timer started.');
  };

  /**
   * Stops the timer if active and calculates completion time.
   */
  const stop = () => {
    if (!isTimerActive.value) {
      console.log('Timer not active or already stopped.');
      return;
    }
    
    if (startTime.value) {
      completionTime.value = Math.round((Date.now() - startTime.value) / 1000); // Time in seconds
      console.log(`Form timer stopped. Completion time: ${completionTime.value}s`);
    } else {
      completionTime.value = null;
    }
    
    isTimerActive.value = false;
    
    // Don't clear local storage on stop - only on reset or explicit removal
  };

  /**
   * Resets the timer state and clears localStorage.
   */
  const reset = () => {
    startTime.value = null;
    completionTime.value = null;
    isTimerActive.value = false;
    
    if (pendingSubmission) {
      pendingSubmission.removeTimer();
    }
    
    console.log('Form timer reset.');
  };

  /**
   * Returns the calculated completion time.
   * @returns {Number|null} Completion time in seconds, or null if not available.
   */
  const getCompletionTime = () => {
    return completionTime.value;
  };

  // Expose reactive state and methods
  return {
    isTimerActive, // Expose reactive status
    completionTime, // Expose reactive completion time
    start,
    stop,
    reset,
    getCompletionTime,
  };
} 