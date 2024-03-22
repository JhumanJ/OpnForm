<template>
  <div class="flex mb-1 input-help">
    <small
      :class="helpClasses"
      class="grow flex"
    >
      <slot name="help">
        <span
          v-if="help"
          class="field-help"
          v-html="help"/>
      </slot>
    </small>
    <slot name="after-help">
      <small class="flex-grow"/>
    </slot>
  </div>
</template>

<script setup>

defineProps({
  helpClasses: {type: String, default: 'text-gray-400 dark:text-gray-500'},
  help: {type: String, required: false}
})
</script>
