<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Profile details</h3>
    <small class="text-gray-600">Update your username and manage your account details.</small>

    <form @submit.prevent="update" @keydown="form.onKeydown($event)" class="mt-3">
      <alert-success class="mb-5" :form="form" :message="$t('info_updated')" />

      <!-- Name -->
      <text-input name="name" :form="form" :label="$t('name')" :required="true" />

      <!-- Email -->
      <text-input name="email" :form="form" :label="$t('email')" :required="true" />

      <!-- Submit Button -->
      <v-button :loading="form.busy" class="mt-4">Save changes</v-button>
    </form>
  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Profile',
    form: new Form({
      name: '',
      email: ''
    })
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    }
  }
}
</script>
