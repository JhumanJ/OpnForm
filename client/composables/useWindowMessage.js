/* eslint-disable */
/**
 * Composable for handling window message communication
 * Provides a consistent interface for cross-window/iframe communication
 */

// Define common message types as constants
export const WindowMessageTypes = {
  LOGIN_COMPLETE: 'login-complete',
  AFTER_LOGIN: 'after-login'
}

export const useWindowMessage = (messageType = null) => {
  const listeners = ref(new Map())
  
  /**
   * Derives an acknowledgment message name from the original message name
   * @param {string} originalMessage - The original message name
   * @returns {string} - The derived acknowledgment message name
   */
  const deriveAcknowledgmentName = (originalMessage) => {
    return `${originalMessage}-acknowledged`
  }
  
  /**
   * Add a listener for a specific message type
   * 
   * @param {string} eventType - The type of message to listen for (defaults to constructor value)
   * @param {Function} callback - The callback function to call when the message is received
   * @param {Object} options - Options for the listener
   * @param {boolean} options.useMessageChannel - Whether to expect and use MessageChannel ports in the event
   * @param {boolean} options.acknowledge - Whether to automatically acknowledge the message
   */
  const listen = (callback, options = {}, eventType = null) => {
    const targetEventType = eventType || messageType
    if (!targetEventType) {
      console.error('No message type provided to listen for')
      return
    }
    
    const { 
      useMessageChannel = true, 
      acknowledge = true 
    } = options
    
    const acknowledgmentName = deriveAcknowledgmentName(targetEventType)
    
    const handler = (event) => {
      // For simple messages
      if (!useMessageChannel && event.data === targetEventType) {
        callback(event)
        return
      }
      
      // For MessageChannel messages
      if (useMessageChannel && event.data === targetEventType && event.ports && event.ports.length > 0) {
        // Send acknowledgement if requested
        if (acknowledge && event.ports[0]) {
          event.ports[0].postMessage(acknowledgmentName)
        }
        
        // Call the callback with the event
        callback(event)
        return
      }
    }
    
    // Add the listener to the window
    window.addEventListener('message', handler)
    
    // Store the handler for cleanup
    listeners.value.set(targetEventType, handler)
    
    // Return a function to remove the listener
    return () => stopListening(targetEventType)
  }
  
  /**
   * Remove a listener for a specific message type
   * 
   * @param {string} eventType - The type of message to stop listening for
   */
  const stopListening = (eventType = null) => {
    const targetEventType = eventType || messageType
    if (!targetEventType) {
      console.error('No message type provided to stop listening for')
      return
    }
    
    const handler = listeners.value.get(targetEventType)
    if (handler) {
      window.removeEventListener('message', handler)
      listeners.value.delete(targetEventType)
    }
  }
  
  /**
   * Send a message to another window
   * 
   * @param {Window} targetWindow - The window to send the message to
   * @param {Object} options - Options for sending the message
   * @param {string} options.eventType - The type of message to send (defaults to constructor value)
   * @param {string} options.targetOrigin - The origin to send the message to, defaults to '*'
   * @param {boolean} options.useMessageChannel - Whether to use MessageChannel for communication
   * @param {number} options.timeout - Timeout in ms for the acknowledgment, defaults to 500ms
   * @param {boolean} options.waitForAcknowledgment - Whether to wait for acknowledgment
   * @returns {Promise} - Resolves when acknowledged or after timeout
   */
  const send = (targetWindow, options = {}) => {
    const { 
      eventType = null,
      targetOrigin = '*', 
      useMessageChannel = true,
      timeout = 500,
      waitForAcknowledgment = true
    } = options
    
    const targetEventType = eventType || messageType
    if (!targetEventType) {
      console.error('No message type provided to send')
      return Promise.reject(new Error('No message type provided'))
    }
    
    const acknowledgmentName = deriveAcknowledgmentName(targetEventType)
    
    if (!useMessageChannel) {
      // Simple message without MessageChannel
      targetWindow.postMessage(targetEventType, targetOrigin)
      return Promise.resolve()
    } else {
      // Using MessageChannel for two-way communication
      return new Promise((resolve) => {
        // Create a message channel for two-way communication
        const channel = new MessageChannel()
        
        // If we expect an acknowledgment, listen for it
        if (waitForAcknowledgment) {
          channel.port1.onmessage = (event) => {
            if (event.data === acknowledgmentName) {
              resolve(true) // Acknowledged
            }
          }
        }
        
        // Send the message with the port
        targetWindow.postMessage(targetEventType, targetOrigin, [channel.port2])
        
        // Set a timeout as fallback
        if (waitForAcknowledgment) {
          setTimeout(() => resolve(false), timeout)
        } else {
          resolve(true)
        }
      })
    }
  }
  
  /**
   * Cleanup all listeners
   * Call this explicitly if needed, though it's automatically called on unmount
   */
  const cleanup = () => {
    listeners.value.forEach((handler, type) => {
      window.removeEventListener('message', handler)
    })
    listeners.value.clear()
  }
  
  // Auto-cleanup when the component is unmounted
  onUnmounted(() => {
    cleanup()
  })
  
  // If messageType was provided on creation, set up a default listener
  const setupDefaultListener = (callback, options = {}) => {
    if (messageType && callback) {
      return listen(callback, options)
    }
  }
  
  return {
    listen,
    stopListening,
    send,
    cleanup,
    setupDefaultListener
  }
} 