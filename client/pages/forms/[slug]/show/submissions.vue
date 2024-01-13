<template>
  <div id="table-page">
    <form-submissions/>
  </div>
</template>

<script setup>
import FormSubmissions from '../../../../components/open/forms/components/FormSubmissions.vue'

const props = {
  form: {type: Object, required: true}
}

definePageMeta({
  middleware: "auth"
})
useOpnSeoMeta({
  title: (props.form) ? 'Form Submissions - ' + props.form.title : 'Form Submissions'
})

</script>
