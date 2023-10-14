<template>
  <div id="table-page">
    <form-submissions/>
  </div>
</template>

<script>
import FormSubmissions from '../../../components/open/forms/components/FormSubmissions.vue'
import SeoMeta from '../../../mixins/seo-meta.js'

export default {
  components: {FormSubmissions},
  props: {
    form: {type: Object, required: true}
  },
  mixins: [SeoMeta],

  data: () => ({}),

  mounted() {
  },

  computed: {
    metaTitle() {
      return (this.form) ? 'Form Submissions - ' + this.form.title : 'Form Submissions'
    },
  },

  methods: {}
}
</script>
