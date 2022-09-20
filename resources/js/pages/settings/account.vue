<template>
  <card title="Account" class="bg-gray-50 dark:bg-notion-dark-light">
    <h3 class="text-lg font-semibold mb-4">
      Your Account
    </h3>

    <p class="text-gray-800 dark:text-gray-200">
      You can delete your account. All your data will be removed. <span class="font-semibold">This cannot be undone.</span>
    </p>

    <!-- Submit Button -->
    <v-button :loading="loading" class="mt-4" color="red" @click="alertConfirm('Do you really want to delete your account?',deleteAccount)">
      Delete my account
    </v-button>
  </card>
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
