export default {
  methods: {
    displayFormModificationAlert (responseData) {
      if (responseData.form && responseData.form.cleanings && Object.keys(responseData.form.cleanings).length > 0) {
        useAlert().warning(responseData.message)
      } else {
        useAlert().success(responseData.message)
      }
    }
  }
}
