<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
        <h1 class="my-6">
          Reset password
        </h1>
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <alert-success :form="form" :message="status" class="mb-4" />

          <!-- Email -->
          <text-input name="email" :form="form" label="Email" :required="true" />

          <!-- Submit Button -->
          <v-button class="w-full" :loading="form.busy">
            Send Password Reset Link
          </v-button>
        </form>
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
export default {
  setup () {
    definePageMeta({
      middleware: "guest"
    })
    useOpnSeoMeta({
      title: 'Reset Password'
    })
  },

  data: () => ({
    status: '',
    form: useForm({
      email: ''
    })
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/password/email')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
