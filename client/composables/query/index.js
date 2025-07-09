/**
 * Callback Chaining Utilities for Query Composables
 * 
 * PROBLEM:
 * When components pass their own onSuccess/onError callbacks to mutations,
 * they override the built-in cache management logic in the composables.
 * This breaks cache updates and other built-in functionality.
 * 
 * SOLUTION:
 * Use these utilities to chain callbacks so both built-in and user-provided
 * callbacks are executed in the correct order.
 * 
 * BEFORE (problematic):
 * ```js
 * const addUser = (workspaceId, options = {}) => {
 *   return useMutation({
 *     mutationFn: (data) => api.addUser(data),
 *     onSuccess: (newUser) => {
 *       // Built-in cache management
 *       queryClient.setQueryData(['users'], (old) => [...old, newUser])
 *     },
 *     ...options // This overrides the onSuccess above!
 *   })
 * }
 * ```
 * 
 * AFTER (fixed):
 * ```js
 * const addUser = (workspaceId, options = {}) => {
 *   const builtInOnSuccess = (newUser) => {
 *     // Built-in cache management
 *     queryClient.setQueryData(['users'], (old) => [...old, newUser])
 *   }
 *   
 *   return useMutation({
 *     mutationFn: (data) => api.addUser(data),
 *     ...chainCallbacks(builtInOnSuccess, null, options)
 *   })
 * }
 * ```
 */

/**
 * Utility function to chain callbacks in mutations
 * This ensures both built-in cache management and user-provided callbacks are executed
 * 
 * @param {Function} builtInOnSuccess - Built-in onSuccess handler for cache management
 * @param {Function} builtInOnError - Built-in onError handler (optional)
 * @param {Object} options - User-provided options that may contain callbacks
 * @returns {Object} - Processed options with chained callbacks
 */
export function chainCallbacks(builtInOnSuccess, builtInOnError, options = {}) {
  const { onSuccess: userOnSuccess, onError: userOnError, ...restOptions } = options
  
  return {
    onSuccess: (...args) => {
      // Call built-in handler first (cache management)
      if (builtInOnSuccess) {
        builtInOnSuccess(...args)
      }
      
      // Then call user-provided handler if it exists
      if (userOnSuccess) {
        userOnSuccess(...args)
      }
    },
    onError: (...args) => {
      // Call built-in error handler first (if exists)
      if (builtInOnError) {
        builtInOnError(...args)
      }
      
      // Then call user-provided error handler if it exists
      if (userOnError) {
        userOnError(...args)
      }
    },
    ...restOptions
  }
}

/**
 * Simplified version when only onSuccess chaining is needed
 * 
 * @param {Function} builtInOnSuccess - Built-in onSuccess handler for cache management
 * @param {Object} options - User-provided options that may contain callbacks
 * @returns {Object} - Processed options with chained callbacks
 */
export function chainOnSuccess(builtInOnSuccess, options = {}) {
  return chainCallbacks(builtInOnSuccess, null, options)
} 