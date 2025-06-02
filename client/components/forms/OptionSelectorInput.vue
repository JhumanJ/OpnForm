<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div
      class="grid"
      :class="[gridClass, { 'gap-2': !seamless }]"
      :style="optionStyle"
      role="listbox"
      :aria-multiselectable="multiple ? 'true' : 'false'"
      :tabindex="disabled ? -1 : 0"
      @keydown="onKeydown"
      ref="root"
    >
      <button
        v-for="(option, idx) in options"
        :key="option[optionKey]"
        class="flex flex-col items-center justify-center p-1.5 border transition-colors text-gray-500 focus:outline-none"
        :class="[
          option.class ? (typeof option.class === 'function' ? option.class(isSelected(option)) : option.class) : {},
          {
            'border-form-color text-form-color bg-form-color/10': isSelected(option),
            'hover:bg-gray-100 border-gray-300': !isSelected(option),
            'opacity-50 pointer-events-none': disabled || option.disabled,
            // Seamless mode: only first and last have radius
            'rounded-lg': !seamless,
            'rounded-l-lg': seamless && idx === 0,
            'rounded-r-lg': seamless && idx === options.length - 1,
            // Seamless mode: overlap borders with negative margin, keep all borders
            '-ml-px': seamless && idx > 0,
            // Seamless mode: z-index hierarchy - selected > hovered/focused > default
            'relative z-20': seamless && isSelected(option),
            'relative z-10': seamless && !isSelected(option) && focusedIdx === idx,
            'relative z-0': seamless && !isSelected(option) && focusedIdx !== idx,
            // Add hover z-index for seamless mode (but lower than selected)
            'hover:z-10': seamless && !isSelected(option)
          }
        ]"
        :aria-selected="isSelected(option) ? 'true' : 'false'"
        :tabindex="disabled || option.disabled ? -1 : 0"
        :disabled="disabled || option.disabled"
        @click="selectOption(option)"
        @focus="focusedIdx = idx"
        @mouseenter="focusedIdx = idx"
        :title="option.tooltip || ''"
        role="option"
      >
        <slot name="icon" :option="option" :selected="isSelected(option)">
          <Icon
            v-if="option.icon"
            :name="isSelected(option) && option.selectedIcon ? option.selectedIcon : option.icon"
            :class="[
              'w-4 h-4',
              option.label ? 'mb-1' : '',
              isSelected(option) ? 'text-form-color' : 'text-inherit',
              option.iconClass ? (typeof option.iconClass === 'function' ? option.iconClass(isSelected(option)) : option.iconClass) : {}
            ]"
          />
        </slot>
        <span
          v-if="option.label || !option.icon"
          class="text-xs"
          :class="{
            'text-form-color': isSelected(option),
            'text-inherit': !isSelected(option),
          }"
        >{{ isSelected(option) ? option.selectedLabel ?? option.label : option.label }}</span>
      </button>
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
import { ref, computed, watch, nextTick } from 'vue'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'

/**
 * OptionSelectorInput.vue
 *
 * A form input component for selecting options in a grid layout with icons.
 * Integrates with the form system using InputWrapper and useFormInput.
 *
 * Props:
 *  - options: Array<{ name, label, icon, selectedIcon?, iconClass?, tooltip?, disabled? }>
 *  - multiple: Boolean (default: false)
 *  - optionKey: String (default: 'name')
 *  - columns: Number (default: 3, for grid layout)
 *  - seamless: Boolean (default: false, removes gaps and only applies radius to first/last items)
 *
 * Features:
 *  - Keyboard navigation (arrow keys, enter/space to select)
 *  - Focus management
 *  - Optional tooltips per option
 *  - Form validation integration
 *  - Notion-style look by default
 *  - Seamless mode for connected button appearance
 */

const props = defineProps({
  ...inputProps,
  options: { type: Array, required: true },
  multiple: { type: Boolean, default: false },
  optionKey: { type: String, default: 'name' },
  columns: { type: Number, default: 3 },
  seamless: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

// Use form input composable
const {
  compVal,
  inputWrapperProps
} = useFormInput(props, { emit })

// Local state
const focusedIdx = ref(-1)
const root = ref(null)

// Computed properties
const gridClass = computed(() => `grid-cols-${props.columns}`)

const optionStyle = computed(() => ({
  '--bg-form-color': props.color
}))

// Methods
function isSelected(option) {
  if (props.multiple) {
    return Array.isArray(compVal.value) && compVal.value.includes(option[props.optionKey])
  }
  return compVal.value === option[props.optionKey]
}

function selectOption(option) {
  if (props.disabled || option.disabled) return
  
  if (props.multiple) {
    let newValue = Array.isArray(compVal.value) ? [...compVal.value] : []
    const idx = newValue.indexOf(option[props.optionKey])
    if (idx > -1) {
      newValue.splice(idx, 1)
    } else {
      newValue.push(option[props.optionKey])
    }
    compVal.value = newValue
  } else {
    compVal.value = isSelected(option) ? null : option[props.optionKey]
  }
}

function onKeydown(e) {
  if (props.disabled) return
  const len = props.options.length
  if (len === 0) return
  
  if (["ArrowRight", "ArrowDown"].includes(e.key)) {
    e.preventDefault()
    focusedIdx.value = (focusedIdx.value + 1) % len
    focusButton(focusedIdx.value)
  } else if (["ArrowLeft", "ArrowUp"].includes(e.key)) {
    e.preventDefault()
    focusedIdx.value = (focusedIdx.value - 1 + len) % len
    focusButton(focusedIdx.value)
  } else if (["Enter", " ", "Spacebar"].includes(e.key)) {
    e.preventDefault()
    if (focusedIdx.value >= 0 && focusedIdx.value < len) {
      selectOption(props.options[focusedIdx.value])
    }
  }
}

function focusButton(idx) {
  nextTick(() => {
    const btns = root.value?.querySelectorAll('button')
    if (btns && btns[idx]) btns[idx].focus()
  })
}

// Watchers
watch(compVal, (val) => {
  // Keep focus on selected
  if (!props.multiple && val != null) {
    const idx = props.options.findIndex(opt => opt[props.optionKey] === val)
    if (idx !== -1) focusedIdx.value = idx
  }
})
</script> 