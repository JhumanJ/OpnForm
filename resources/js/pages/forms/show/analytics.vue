<template>
  <div>
    <h3 class="font-semibold mt-4 text-xl">
      Form Analytics (last 30 days)
    </h3>
    <form-stats :form="form"/>
  </div>
</template>

<script>
import FormStats from '../../../components/open/forms/components/FormStats'
import SeoMeta from '../../../mixins/seo-meta'

export default {
  components: {FormStats},
  props: {
    form: { type: Object, required: true }
  },
  mixins: [SeoMeta],

  data: () => ({
  }),

  mounted() {},

  computed: {
    metaTitle() {
      return (this.form) ? 'Form Analytics - '+this.form.title : 'Form Analytics'
    },
  },

  methods: {
    
  }
}
</script>