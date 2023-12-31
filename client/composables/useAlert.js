const { notify } = useNotification()

export const useAlert = () => {

  function success(message, autoClose = 10000) {
    notify({
      title: 'Success',
      text: message,
      type: 'success',
      duration: autoClose
    })
  }

  function error(message, autoClose = 10000) {
    notify({
      title: 'Error',
      text: message,
      type: 'error',
      duration: autoClose
    })
  }

  function warning(message, autoClose = 10000) {
    notify({
      title: 'Warning',
      text: message,
      type: 'warning',
      duration: autoClose
    })
  }

  function confirm(message, success, failure = ()=>{}, autoClose = 10000) {
    notify({
      title: 'Confirm',
      text: message,
      type: 'confirm',
      duration: autoClose,
      data: {
        success,
        failure
      }
    })
  }

  return {
    success,
    error,
    warning,
    confirm
  }
}
