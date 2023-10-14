<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Password</h3>
    <small class="text-gray-600">Manage your password.</small>

    <form @submit.prevent="update" @keydown="form.onKeydown($event)" class="mt-3">
      <alert-success class="mb-5" :form="form" :message="$t('password_updated')" />

      <!-- Password -->
      <text-input native-type="password"
                  name="password" :form="form" :label="$t('password')" :required="true"
      />

      <!-- Password Confirmation-->
      <text-input native-type="password"
                  name="password_confirmation" :form="form" :label="$t('confirm_password')" :required="true"
      />

      <!-- Submit Button -->
      <v-button :loading="form.busy" class="mt-4">Update password</v-button>
    </form>
  </div>
</template>

<script>
import Form from 'vform'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Password',
    form: new Form({
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('/api/settings/password')

      this.form.reset()
    }
  }
}
</script>
