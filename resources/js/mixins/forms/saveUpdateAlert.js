export default {
  methods: {
    displayFormModificationAlert (responseData) {
      if (responseData.form.cleanings && Object.keys(responseData.form.cleanings).length > 0) {
        this.alertWarning(responseData.message)
      } else {
        this.alertSuccess(responseData.message)
      }
    }
  }
}
