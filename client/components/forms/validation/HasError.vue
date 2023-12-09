<template>
  <transition name="fade">
    <div v-if="errorMessage" class="has-error text-sm text-red-500 -bottom-3"
         v-html="errorMessage"
    />
  </transition>
</template>

<script>
export default {
  name: 'HasError',
  props: {
    form: {
      type: Object,
      required: true
    },
    field: {
      type: String,
      required: true
    }
  },
  computed: {
    errorMessage () {
      if (!this.form || !this.form.errors || !this.form.errors.any()) return null
      const subErrorsKeys = Object.keys(this.form.errors.all()).filter((key) => {
        return key.startsWith(this.field) && key !== this.field
      })
      const baseError = this.form.errors.get(this.field) ?? (subErrorsKeys.length ? 'This field has some errors:' : null)
      // If no error and no sub errors, return
      if (!baseError) return null

      return `<p class="text-red-500">${baseError}</p><ul class="list-disc list-inside">${subErrorsKeys.map((key) => {
        return '<li>' + this.getSubError(key) + '</li>'
      })}</ul>`
    }
  },
  methods: {
    getSubError (subErrorKey) {
      return this.form.errors.get(subErrorKey).replace(subErrorKey, 'item')
    }
  }
}
</script>
