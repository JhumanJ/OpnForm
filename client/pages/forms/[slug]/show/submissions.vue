<template>
  <div id="table-page">
    <form-submissions/>
  </div>
</template>

<script>
import FormSubmissions from '../../../../components/open/forms/components/FormSubmissions.vue'

export default {
  components: {FormSubmissions},
  props: {
    form: {type: Object, required: true}
  },

  setup (props) {
    definePageMeta({
      middleware: "auth"
    })
    useOpnSeoMeta({
      title: (props.form) ? 'Form Submissions - '+props.form.title : 'Form Submissions'
    })
  },

  data: () => ({}),

  mounted() {
  },

  computed: {
  },

  methods: {}
}
</script>
