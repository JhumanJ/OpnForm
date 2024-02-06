<template>
  <div>
    <h3 class="font-semibold mt-4 text-xl">
      Form Analytics (last 30 days)
    </h3>
    <form-stats :form="form"/>
  </div>
</template>

<script setup>
import FormStats from '~/components/open/forms/components/FormStats.vue'

const props = defineProps({
  form: {type: Object, required: true}
})

definePageMeta({
  middleware: "auth"
})
useOpnSeoMeta({
  title: (props.form) ? 'Form Analytics - ' + props.form.title : 'Form Analytics'
})
</script>
