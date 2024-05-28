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

  function remove (id) {
    useToast().remove(id)
  }

  return {
    success,
    error,
    warning,
    confirm,
    remove
  }
}
