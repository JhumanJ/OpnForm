<template>
  <label
    :for="nativeFor"
    class="input-label"
    :class="[
      theme.default.label,
      { 'uppercase text-xs': uppercaseLabels, 'text-sm': !uppercaseLabels },
    ]"
  >
    <slot>
      {{ label }}
      <span
        v-if="required"
        class="text-red-500 required-dot"
      >*</span>
    </slot>
  </label>
</template>

<script>
export default {
  name: 'InputLabel',

  props: {
    nativeFor: { type: String, default: null },
    theme: { type: Object, required: true },
    uppercaseLabels: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
  },
}
</script>
