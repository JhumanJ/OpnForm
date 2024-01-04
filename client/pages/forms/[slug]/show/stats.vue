<template>
  <div>
    <h3 class="font-semibold mt-4 text-xl">
      Form Analytics (last 30 days)
    </h3>
    <form-stats :form="form"/>
  </div>
</template>

<script>
import FormStats from '../../../../components/open/forms/components/FormStats.vue'

export default {
  components: {FormStats},

  props: {
    form: {type: Object, required: true},
  },

  setup (props) {
    useOpnSeoMeta({
      title: (props.form) ? 'Form Analytics - '+props.form.title : 'Form Analytics'
    })
  },

  computed: {
  }
}
</script>
