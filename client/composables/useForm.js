import Form from "~/composables/lib/vForm/Form.js"

export const useForm = (formData = {}) => {
  return reactive(new Form(formData))
}
