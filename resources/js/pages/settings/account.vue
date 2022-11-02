<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Danger zone</h3>
    <small class="text-gray-600">This will permanently delete your entire account. All your forms, submissions and workspaces will be deleted.</small>
  
    <p class="mt-3 font-semibold text-red-500">
      This cannot be undone.
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

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: 'Account' }
  },

  data: () => ({
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
