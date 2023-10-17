<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Cài đặt quản trị viên</h3>
    <small class="text-gray-600">Quản lý cài đặt.</small>


    <h3 class="mt-3 text-lg font-semibold mb-4">
      Công vụ
    </h3>
    <div class="flex flex-wrap mb-5">
      <a href="/stats">
        <v-button class="mx-1" color="gray" shade="lighter">
          Thống kê
        </v-button>
      </a>
      <a href="/horizon">
        <v-button class="mx-1" color="gray" shade="lighter">
          Horizon
        </v-button>
      </a>
    </div>
    <h3 class="text-lg font-semibold mb-4">
      Bắt cắt danh tính người dùng
    </h3>
    <form @submit.prevent="impersonate" @keydown="form.onKeydown($event)">
      <!-- Password -->
      <text-input name="identifier" :form="form" label="Identifier" :required="true"
        help="User Id, User Email or Form Slug" />

      <!-- Submit Button -->
      <v-button :loading="loading" class="mt-4">Bắt cắt danh tính người dùng</v-button>
    </form>
  </div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: {},
  middleware: 'admin',
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Quản trị viên',
    form: new Form({
      identifier: ''
    }),
    loading: false
  }),

  methods: {
    async impersonate() {
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
