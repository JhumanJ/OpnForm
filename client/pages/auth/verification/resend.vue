<template>
  <div class="row">
    <div class="col-lg-8 m-auto px-4">
      <h1 class="my-6">
        Verify Email
      </h1>
      <form @submit.prevent="send" @keydown="form.onKeydown($event)">
        <alert-success :form="form" :message="status" />

        <!-- Email -->
        <text-input name="email" :form="form" label="Email" :required="true" />

        <!-- Submit Button -->
        <div class="form-group row">
          <div class="col-md-9 ml-md-auto">
            <v-button :loading="form.busy">
              Send Verification Link
            </v-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

export default {
  middleware: 'guest',

  data: () => ({
    metaTitle: 'Verify Email',
    status: '',
    form: useForm({
      email: ''
    })
  }),

  created () {
    if (this.$route.query.email) {
      this.form.email = this.$route.query.email
    }
  },

  methods: {
    async send () {
      const { data } = await this.form.post('/api/email/resend')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
