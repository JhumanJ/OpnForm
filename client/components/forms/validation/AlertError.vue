<template>
  <div v-if="form.errors.any()" class="alert alert-danger alert-dismissible" role="alert">
    <button v-if="dismissible" type="button" class="close" aria-label="Close" @click="dismiss">
      <span aria-hidden="true">&times;</span>
    </button>

    <slot>
      <div v-if="form.errors.has('error')" v-html="form.errors.get('error')"/>
      <div v-else v-html="message"/>
    </slot>
  </div>
</template>

<script>
import Alert from './Alert.js'

export default {
  name: 'AlertError',

  extends: Alert,

  props: {
    message: {
      type: String,
      default: 'There were some problems with your input.'
    }
  }
}
</script>
