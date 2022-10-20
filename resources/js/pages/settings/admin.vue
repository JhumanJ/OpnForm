<template>
  <card title="Admin" class="bg-gray-50 dark:bg-notion-dark-light">
    <h3 class="text-lg font-semibold mb-4">
      Tools
    </h3>
    <div class="flex flex-wrap mb-10">
      <a href="/stats">
        <v-button class="mx-1" color="gray" shade="lighter">
          Stats
        </v-button>
      </a>
      <a href="/horizon">
        <v-button class="mx-1" color="gray" shade="lighter">
          Horizon
        </v-button>
      </a>
    </div>
    <h3 class="text-lg font-semibold mb-4">
      Impersonate User
    </h3>
    <form @submit.prevent="impersonate" @keydown="form.onKeydown($event)">
      <!-- Password -->
      <text-input name="identifier" :form="form" label="Identifier"
                  :required="true" help="User Id, User Email or Form Slug"
      />

      <!-- Submit Button -->
      <v-button :loading="loading" class="mt-4 w-full">
        Impersonate User
      </v-button>
    </form>
  </card>
</template>

<script>
import Form from 'vform'
import axios from 'axios'

export default {
  components: { },
  middleware: 'admin',
  scrollToTop: false,

  metaInfo () {
    return { title: 'Admin' }
  },

  data: () => ({
    form: new Form({
      identifier: ''
    }),
    loading: false
  }),

  methods: {
    async impersonate () {
      this.loading = true
      this.$store.commit('auth/startImpersonating')
      axios.get('/api/admin/impersonate/' + encodeURI(this.form.identifier)).then(async (response) => {
        this.loading = false

        // Save the token.
        this.$store.dispatch('auth/saveToken', {
          token: response.data.token,
          remember: false
        })

        // Fetch the user.
        await this.$store.dispatch('auth/fetchUser')

        // Redirect to the dashboard.
        this.$store.commit('open/workspaces/set', [])
        this.$router.push({ name: 'home' })
      }).catch((error) => {
        this.alertError(error.response.data.message)
        this.loading = false
      })

      // this.form.reset()
    }
  }
}
</script>
