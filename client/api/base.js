import Form from '~/composables/lib/vForm/Form'

export const apiService = {
  // GET requests - supports both useFetch and $fetch
  async get(endpoint, options = {}) {
    const { server = true, ...fetchOptions } = options
    
    if (server) {
      // Use useFetch for SSR benefits (default)
      const response = await useOpnApi(endpoint, fetchOptions)
      
      if (response.pending.value) {
        await response.refresh()
      }
      
      if (response.error.value) {
        throw response.error.value
      }
      
      return response.data.value
    } else {
      // Use $fetch for simple client-side fetching
      return await opnFetch(endpoint, fetchOptions)
    }
  },

  // POST requests
  async post(endpoint, data = null, options = {}) {
    return await this.mutate(endpoint, { 
      method: 'POST', 
      body: data, 
      ...options 
    })
  },

  // PUT requests  
  async put(endpoint, data = null, options = {}) {
    return await this.mutate(endpoint, { 
      method: 'PUT', 
      body: data, 
      ...options 
    })
  },

  // PATCH requests
  async patch(endpoint, data = null, options = {}) {
    return await this.mutate(endpoint, { 
      method: 'PATCH', 
      body: data, 
      ...options 
    })
  },

  // DELETE requests
  async delete(endpoint, options = {}) {
    return await this.mutate(endpoint, { 
      method: 'DELETE', 
      ...options 
    })
  },

  // Legacy fetch method (backward compatibility)
  async fetch(endpoint, options = {}) {
    return await this.get(endpoint, options)
  },

  // Mutation handler (for all non-GET requests)
  async mutate(endpoint, options = {}) {
    const { body, ...restOptions } = options
    
    // If body is a Form instance, use its submit method
    if (body instanceof Form) {
      const method = (options.method || 'POST').toLowerCase()
      return await body[method](endpoint, options)
    }
    
    // Otherwise use regular opnFetch
    return await opnFetch(endpoint, {
      method: options.method || 'POST',
      body,
      ...restOptions
    })
  }
} 