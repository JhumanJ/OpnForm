/**
 * @fileoverview Service responsible for managing the form completion timer.
 * It handles starting, stopping, resetting the timer, persisting the time
 * using the pendingSubmission composable, and providing the current completion time.
 */
import { reactive } from 'vue'
import { pendingSubmission } from '~/composables/forms/pendingSubmission.js'

export class FormTimerService {
  constructor(formConfig) {
    this.formConfig = formConfig
    // Initialize pending submission helper specific to this form
    // Ensure it's client-side safe
    this.pendingSubmission = import.meta.client ? pendingSubmission(this.formConfig) : null

    // Internal state using a reactive object
    console.log('FormTimerService Constructor: Initializing reactive state')
    this.state = reactive({
        intervalId: null,
        completionTime: 0
    })
    console.log('FormTimerService Constructor: State initialized:', this.state)
  }

  /**
   * Starts the timer.
   * Loads initial time from storage if available.
   * Sets up an interval to increment time and persist it.
   */
  start() {
    // Check state from reactive object
    if (this.state.intervalId || !import.meta.client) {
        console.log('FormTimerService Start: Timer already running or SSR. Exiting.')
        // Timer already running or SSR, do nothing
        return
    }

    console.log('FormTimerService Start: Before try block. State:', this.state)
    console.log('Starting form timer...')
    try {
        // Load initial time from pending submission storage
        const storedTime = this.pendingSubmission ? parseInt(this.pendingSubmission.getTimer() || '0', 10) : 0
        // Set value in reactive state
        this.state.completionTime = Math.max(storedTime, 0)

        console.log('FormTimerService Start: Before setInterval. State:', this.state)
        // Start the interval
        this.state.intervalId = setInterval(() => {
          this.state.completionTime++ // Directly increment state property
          // Persist current time using pendingSubmission
          if (this.pendingSubmission) {
              this.pendingSubmission.setTimer(this.state.completionTime)
          }
        }, 1000)
    } catch (e) {
        console.error("Error starting FormTimerService:", e)
    }
  }

  /**
   * Stops the timer interval.
   */
  stop() {
    console.log('FormTimerService Stop: Entering. State:', this.state)
    if (this.state.intervalId) {
      console.log('Stopping form timer.')
      clearInterval(this.state.intervalId)
      this.state.intervalId = null // Reset state property
      console.log('FormTimerService Stop: State after clearing interval:', this.state)
    } else {
        console.log('FormTimerService Stop: No interval ID found in state.')
    }
  }

  /**
   * Resets the timer.
   * Stops the interval, resets completion time to 0, and clears persisted time.
   */
  reset() {
    console.log('Resetting form timer.')
    console.log('FormTimerService Reset: Entering. State:', this.state)
    
    this.stop()
    // Reset completion time in reactive state
    this.state.completionTime = 0
    console.log('FormTimerService Reset: State after resetting time:', this.state)
    
    // Clear persisted timer value
    if (this.pendingSubmission && import.meta.client) {
      this.pendingSubmission.removeTimer()
    }
  }

  /**
   * Gets the current completion time.
   * @returns {number} The completion time in seconds.
   */
  getCompletionTime() {
    return this.state.completionTime
  }
} 