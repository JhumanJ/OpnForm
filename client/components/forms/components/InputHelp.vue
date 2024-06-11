<template>
  <div
    v-if="shouldRender"
    class="flex mb-1 input-help"
  >
    <small
      :class="helpClasses"
      class="grow flex"
    >
      <slot>
        <span
          v-if="help"
          class="field-help"
          v-html="help"
        />
      </slot>
    </small>
    <slot name="after-help">
      <small class="flex-grow" />
    </slot>
  </div>
</template>

<script setup>
const slots = useSlots()
const props = defineProps({
  helpClasses: { type: String, default: "text-gray-400 dark:text-gray-500" },
  help: { type: String, required: false },
})

const shouldRender = computed(() => {
  return props.help || !!slots.default || !!slots['after-help']
})
</script>
