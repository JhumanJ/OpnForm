<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Chi tiết thanh toán</h3>
    <small class="text-gray-600">Quản lý thanh toán của bạn. Tải hóa đơn, cập nhật gói dịch vụ hoặc hủy nó bất cứ lúc
      nào.</small>

    <div class="mt-4">
      <v-button color="gray" shade="light" :loading="billingLoading" @click.prevent="openBillingDashboard">
        Quản lý Đăng ký
      </v-button>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import VButton from '../../components/common/Button.vue'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: { VButton },
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Hóa đơn',
    billingLoading: false
  }),

  methods: {
    openBillingDashboard() {
      this.billingLoading = true
      axios.get('/api/subscription/billing-portal').then((response) => {
        const url = response.data.portal_url
        window.location = url
      }).catch((error) => {
        this.alertError(error.response.data.message)
      }).finally(() => {
        this.billingLoading = false
      })
    }
  }
}
</script>
