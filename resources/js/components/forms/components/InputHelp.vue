<template>
  <div class="flex mb-1 input-help">
    <small :class="theme.default.help" class="grow flex">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <slot name="after-help">
      <small class="flex-grow"  />
    </slot>
  </div>
</template>

<script>
export default {
  name: 'InputHelp',

  props: {
    theme: { type: Object, required: true },
    help: { type: String, required: false }
  }
}
</script>
