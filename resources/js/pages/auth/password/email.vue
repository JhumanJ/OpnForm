<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
        <h1 class="my-6">
          {{ $t('reset_password') }}
        </h1>
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <alert-success :form="form" :message="status" class="mb-4" />

          <!-- Email -->
          <text-input name="email" :form="form" :label="$t('email')" :required="true" />

          <!-- Submit Button -->
          <v-button class="w-full" :loading="form.busy">
            {{ $t('send_password_reset_link') }}
          </v-button>
        </form>
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import Form from 'vform'
import OpenFormFooter from '../../../components/pages/OpenFormFooter.vue'
import SeoMeta from '../../../mixins/seo-meta.js'

export default {
  middleware: 'guest',
  components: {
    OpenFormFooter
  },

  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Reset Password',
    status: '',
    form: new Form({
      email: ''
    })
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/api/password/email')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
