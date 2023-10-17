<template>
  <div>
    <h3 class="font-semibold mt-4 text-xl">
      Phân tích biểu mẫu (30 ngày vừa qua)
    </h3>
    <form-stats :form="form" />
  </div>
</template>

<script>
import FormStats from '../../../components/open/forms/components/FormStats.vue'
import SeoMeta from '../../../mixins/seo-meta.js'

export default {
  name: 'Stats',
  components: { FormStats },
  props: {
    form: { type: Object, required: true },
  },
  mixins: [SeoMeta],

  data: () => ({}),

  computed: {
    metaTitle() {
      return (this.form ? ('Phân tích biểu mẫu - ' + this.form.title) : 'Phân tích biểu mẫu')
    }
  }
}
</script>
