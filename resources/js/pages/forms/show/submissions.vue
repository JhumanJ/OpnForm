<template>
  <div id="table-page">
    <form-submissions/>
  </div>
</template>

<script>
import FormSubmissions from '../../../components/open/forms/components/FormSubmissions'

export default {
  components: {FormSubmissions},
  props: {
    form: { type: Object, required: true }
  },

  metaInfo() {
    return {title: (this.form) ? 'Form Submissions - '+this.form.title : 'Form Submissions'}
  },

  data: () => ({
  }),

  mounted() {},

  computed: {},

  methods: {
    
  }
}
</script>
