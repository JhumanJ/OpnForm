export default {
  methods: {
    displayFormModificationAlert(responseData) {
      if (responseData.form_cleaning && Object.keys(responseData.form_cleaning).length > 0) {
        let message = responseData.message + '<br/>'
        Object.keys(responseData.form_cleaning).forEach((key) => {
          const fieldName = key.charAt(0).toUpperCase() + key.slice(1)
          let fieldInfo = "<br/>" + fieldName + "<br/><ul class='list-disc list-inside'>"
          responseData.form_cleaning[key].forEach((msg) => {
              fieldInfo = fieldInfo + "<li>" + msg +"</li>"
          })
          message = message + fieldInfo + "<ul/>"
        })
        this.alertWarning(message)
      } else {
        this.alertSuccess(responseData.message)
      }
    }
  }
}
