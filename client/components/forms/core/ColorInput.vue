<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <div class="flex items-center">
      <input
        :id="id ? id : name"
        v-model="compVal"
        :disabled="disabled ? true : null"
        type="color"
        class="mr-2"
        :name="name"
      >
      <slot name="label">
        <span
          :class="[
            theme.SelectInput.fontSize,
          ]"
        >{{ label }}
          <span
            v-if="required"
            class="text-red-500 required-dot"
          >*</span></span>
      </slot>
    </div>

    <template #help>
      <slot name="help" />
    </template>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script setup>
import { inputProps, useFormInput } from "../useFormInput.js"

const props = defineProps({
  ...inputProps,
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const { compVal, inputWrapperProps} = useFormInput(props, { emit })
</script>
