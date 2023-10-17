<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Vùng nguy hiểm</h3>
    <p class="text-gray-600 text-sm mt-2">
      Điều này sẽ xóa vĩnh viễn toàn bộ tài khoản của bạn. Tất cả các biểu mẫu, bài nộp và không gian làm việc của bạn sẽ
      bị xóa.
      <span class="text-red-500">
        Hành động này không thể được hoàn tác.
      </span>
    </p>

    <!-- Submit Button -->
    <v-button :loading="loading" class="mt-4" color="red"
      @click="alertConfirm('Do you really want to delete your account?', deleteAccount)">
      Xóa tài khoản
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
    metaTitle: 'Tài khoản',
    form: new Form({
      identifier: ''
    }),
    loading: false
  }),

  methods: {
    async deleteAccount() {
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
