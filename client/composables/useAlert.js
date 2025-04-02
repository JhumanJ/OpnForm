export function useAlert () {

  function success (message, autoClose = 10000, options = {}) {
    return useToast().add({
      icon: 'i-heroicons-check-circle',
      title: options.title ?? 'Success',
      description: message,
      color: 'green',
      timeout: autoClose,
      ...options
    })
  }

  function error (message, autoClose = 10000, options = {}) {
    return useToast().add({
      icon: 'i-heroicons-exclamation-circle',
      title: options.title ?? 'Error',
      description: message,
      color: 'red',
      timeout: autoClose,
      ...options
    })
  }

  function warning (message, autoClose = 10000, options = {}) {
    return useToast().add({
      icon: 'i-heroicons-exclamation-triangle',
      title: options.title ?? 'Warning',
      description: message,
      color: 'yellow',
      timeout: autoClose,
      ...options
    })
  }

  function confirm (
    message,
    onSuccess,
    onFailure = null,
    autoClose = 10000,
    options = {}
  ) {
    return useToast().add({
      icon: 'i-heroicons-question-mark-circle',
      title: options.title ?? 'Are you sure?',
      description: message,
      color: 'blue',
      timeout: autoClose,
      actions: [
        { label: options.successLabel ?? 'Yes', click: onSuccess },
        { label: options.failureLabel ?? 'No', click: onFailure }
      ],
      ...options
    })
  }

  function formValidationError(error, autoClose = 10000, options = {}) {
    if (!error || !error.errors) {
      return error(error.message || 'An unknown validation error occurred', autoClose, options)
    }

    // Count total errors
    const errorCount = Object.keys(error.errors).length

    // Format error messages as HTML
    let description = ''
    if (Object.keys(error.errors).length > 0) {
      const errorLines = Object.entries(error.errors)
        .map(([_, messages]) => {
          // Format each message
          const formattedMessages = messages.map(message => {
            return `<li>${message}</li>`
          })
          
          return formattedMessages.join('')
        })
        .join('')
      
      description = `<ul class="list-disc pl-4">${errorLines}</ul>`
    }

    // Add count of errors to the title
    const title = options.title || (errorCount > 1 
      ? `Validation Error (${errorCount} fields)` 
      : 'Validation Error')

    return useToast().add({
      icon: 'i-heroicons-x-circle',
      title,
      description,
      color: 'red',
      timeout: autoClose,
      html: true,
      ...options
    })
  }

  function remove (id) {
    useToast().remove(id)
  }

  return {
    success,
    error,
    warning,
    confirm,
    remove,
    formValidationError
  }
}
