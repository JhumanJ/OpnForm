import { ref } from 'vue';

/**
 * @fileoverview Composable for managing form completion time.
 */
export function useFormTimer(formConfig) {
  // Reactive state for timer
  const startTime = ref(null);
  const completionTime = ref(null);
  const isTimerActive = ref(false);

  /**
   * Starts the timer if enabled in formConfig and not already active.
   */
  const start = () => {
    if (!formConfig?.value?.track_completion_time || isTimerActive.value) {
      if (isTimerActive.value) console.log('Timer already active.');
      else console.log('Timer disabled by config.');
      return;
    }
    if (!startTime.value) {
        startTime.value = Date.now(); // Set start time only if not already set (e.g., after reset)
    }
    isTimerActive.value = true;
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
    if (startTime.value && formConfig?.value?.track_completion_time) {
      completionTime.value = Math.round((Date.now() - startTime.value) / 1000); // Time in seconds
      console.log(`Form timer stopped. Completion time: ${completionTime.value}s`);
    } else {
        completionTime.value = null; // Ensure null if tracking disabled or timer never started properly
    }
    isTimerActive.value = false;
  };

  /**
   * Resets the timer state.
   */
  const reset = () => {
    startTime.value = null;
    completionTime.value = null;
    isTimerActive.value = false;
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