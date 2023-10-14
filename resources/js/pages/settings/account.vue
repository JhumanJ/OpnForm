<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Danger zone</h3>
    <p class="text-gray-600 text-sm mt-2">
      This will permanently delete your entire account. All your forms, submissions and workspaces will be deleted.
    <span class="text-red-500">
      This cannot be undone.
    </span>
    </p>

    <!-- Submit Button -->
    <v-button :loading="loading" class="mt-4" color="red" @click="alertConfirm('Do you really want to delete your account?',deleteAccount)">
      Delete account
    </v-button>
  </div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Account',
    form: new Form({
      identifier: ''
    }),
    loading: false
  }),

  methods: {
    async deleteAccount () {
      this.loading = true
      axios.delete('/api/user').then(async (response) => {
        this.loading = false
        this.alertSuccess(response.data.message)
        // Log out the user.
        await this.$store.dispatch('auth/logout')

        // Redirect to login.
        this.$router.push({ name: 'login' })
      }).catch((error) => {
        this.alertError(error.response.data.message)
        this.loading = false
      })
    }
  }
}
</script>
