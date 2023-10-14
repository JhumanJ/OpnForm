<template>
  <div class="row">
    <div class="col-lg-8 m-auto px-4">
      <h1 class="my-6">
        {{ $t('verify_email') }}
      </h1>
      <form @submit.prevent="send" @keydown="form.onKeydown($event)">
        <alert-success :form="form" :message="status" />

        <!-- Email -->
        <text-input name="email" :form="form" :label="$t('email')" :required="true" />

        <!-- Submit Button -->
        <div class="form-group row">
          <div class="col-md-9 ml-md-auto">
            <v-button :loading="form.busy">
              {{ $t('send_verification_link') }}
            </v-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import SeoMeta from '../../../mixins/seo-meta.js'

export default {
  middleware: 'guest',
  mixins: [SeoMeta],
  
  data: () => ({
    metaTitle: 'Verify Email',
    status: '',
    form: new Form({
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
