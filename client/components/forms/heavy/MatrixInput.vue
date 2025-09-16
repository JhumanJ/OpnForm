<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>
    <div :class="ui.container()">
      <table class="w-full table-auto">
        <thead class="">
          <tr>
            <th class="ltr:text-left rtl:text-right w-auto max-w-xs" :class="ui.headerCell()" />
            <td
              v-for="column in columns"
              :key="column"
              class="ltr:border-l rtl:border-r rtl:!border-l-0 max-w-24 overflow-hidden"
              :class="ui.cell()"
            >
              <div :class="ui.headerCell()">
                {{ column }}
              </div>
            </td>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, rowIndex) in rows"
            :key="rowIndex"
            class="border-t border-neutral-300"
            role="radiogroup"
            :aria-label="`${row} options`"
          >
            <td class="ltr:text-left rtl:text-right w-auto max-w-24 overflow-hidden">
              <div :class="ui.rowCell()">
                {{ row }}
              </div>
            </td>
            <td
              v-for="column in columns"
              :key="row + column"
              class="ltr:border-l rtl:border-r rtl:!border-l-0"
              role="radio"
              :tabindex="props.disabled ? -1 : 0"
              :aria-checked="compVal && compVal[row] === column"
              :class="[
                ui.cell(),
                ui.cellHover(),
                ui.option()
              ]"
              @click="onSelect(row, column)"
              @keydown="onKeyDown($event, row, column)"
            >
              <div :class="ui.iconWrapper()">
                <RadioButtonIcon
                  v-if="compVal"
                  :key="row+column"
                  :is-checked="compVal[row] === column"
                  :color="color"
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
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
import { watch } from "vue"
import { inputProps, useFormInput } from "../useFormInput.js"
import RadioButtonIcon from "../core/components/RadioButtonIcon.vue"
import { matrixInputTheme } from '~/lib/forms/themes/matrix-input.theme.js'

const props = defineProps({
  ...inputProps,
  rows: { type: Array, required: true },
  columns: { type: Array, required: true },
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const { compVal, inputWrapperProps, ui } = useFormInput(props, { emit }, {
  variants: matrixInputTheme
})

const onSelect = (row, column) => {
  if (props.disabled) {
    return
  }

  // Create a new object to ensure reactivity is triggered correctly.
  const newValue = { ...(compVal.value || {}) }

  if (newValue[row] === column && !props.required) {
    // Unset the value if it's already selected and not required
    newValue[row] = null
  } else {
    newValue[row] = column
  }
  
  // Assigning a new object to compVal.value will trigger the setter in useFormInput
  compVal.value = newValue
}

const onKeyDown = (event, row, column) => {
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault()
    onSelect(row, column)
  }
}

watch(compVal, (val) => {
  if (!val || typeof val !== 'object') {
    compVal.value = {}
  }
}, { immediate: true, deep: true })
</script>
