<template />

<script>
import { mapGetters } from 'vuex'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: {},
  layout: 'default',
  middleware: 'auth',
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Lỗi',
  }),

  mounted() {
    this.$router.push({ name: 'pricing' })
    this.alertError('Rất tiếc, chúng tôi không thể xác nhận đăng ký của bạn. Vui lòng thử lại và liên hệ với chúng tôi nếu vấn đề vẫn tiếp tục.')
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check'
    })
  }
}
</script>
