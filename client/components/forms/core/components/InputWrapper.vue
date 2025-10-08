<template>
  <div
    :class="wrapperClasses"
    :style="inputStyle"
  >
    <slot name="label">
      <VTransition name="fadeHeight">
      <InputLabel
        v-if="label && !hideFieldName"
        :label="label"
        :required="required"
        :native-for="id ? id : name"
        :uppercase-labels="uppercaseLabels"
        :theme="theme"
        :size="resolvedSize"
        :presentation="presentationStyle"
        :ui="ui?.label"
      />
      </VTransition>
    </slot>
    
    <slot
      v-if="helpPosition === 'above_input'"
      name="help"
    >
    <VTransition name="fadeHeight">
      <InputHelp
        :help="help"
        :help-classes="ui.help()"
      >
        <template #after-help v-if="!!$slots['bottom_after_help']">
          <slot name="bottom_after_help" />
        </template>
      </InputHelp>
    </VTransition>
    </slot>
    <template v-if="media && media.url">
      <div :class="ui.media()">
        <BlockMediaLayout
          :image="media"
          :fallback-height="''"
          :class="ui.mediaComponent()"
          :img-class="ui.mediaImg()"
        />
      </div>
    </template>

    <slot />

    <slot
      v-if="helpPosition === 'below_input'"
      name="help"
    >
    <VTransition name="fadeHeightDown">
      <InputHelp
        :help="help"
        :help-classes="ui.help()"
      >
        <template #after-help v-if="!!$slots['bottom_after_help']">
          <slot name="bottom_after_help" />
        </template>
      </InputHelp>
    </VTransition>
    </slot>
    <slot name="error">
      <VTransition name="fadeHeightDown">
      <has-error
        v-if="hasValidation && form"
        :form="form"
        :field-id="name"
        :field-name="label"
        :error-classes="ui.error()"
      />
    </VTransition>
    </slot>
  </div>
</template>

<script setup>
import InputLabel from './InputLabel.vue'
import InputHelp from './InputHelp.vue'
import {twMerge} from "tailwind-merge"
import { tv } from "tailwind-variants"
import { inputWrapperTheme } from "~/lib/forms/themes/input-wrapper.theme.js"
import BlockMediaLayout from '~/components/open/forms/components/BlockMediaLayout.vue'

const props = defineProps({
  id: { type: String, required: false },
  name: { type: String, required: false },
  label: { type: String, required: false },
  form: { type: Object, required: false },
  wrapperClass: { type: String, required: false },
  inputStyle: { type: Object, required: false },
  help: { type: String, required: false },
  helpPosition: { type: String, default: 'below_input' },
  uppercaseLabels: { type: Boolean, default: true },
  hideFieldName: { type: Boolean, default: true },
  required: { type: Boolean, default: false },
  hasValidation: { type: Boolean, default: true },
  media: { type: Object, default: null },
  // Theme configuration as strings for tailwind-variants
  theme: {type: String, default: 'default'},
  size: {type: String, default: null}, 
  borderRadius: {type: String, default: null},
  ui: {type: Object, default: () => ({})}
})

// Inject theme values for centralized resolution
const injectedSize = inject('formSize', null)
const injectedBorderRadius = inject('formBorderRadius', null)

// Resolve size with proper reactivity
const resolvedSize = computed(() => {
  return props.size || injectedSize?.value || 'md'
})

const resolvedBorderRadius = computed(() => {
  return props.borderRadius || injectedBorderRadius?.value || 'small'
})

const injectedPresentationStyle = computed(() => {
  return inject('formPresentationStyle', ref('classic'))?.value || 'classic'
})

// OPTIMIZED: Single computed following Nuxt UI pattern
const ui = computed(() => {
  return tv(inputWrapperTheme, props.ui)({
    size: resolvedSize.value,
    borderRadius: resolvedBorderRadius.value,
    mediaStyle: 'intrinsic',
    presentation: injectedPresentationStyle.value
  })
})

// Wrapper classes with twMerge operation - makes sense as computed property
const wrapperClasses = computed(() => twMerge(ui.value.wrapper(), props.wrapperClass))

const presentationStyle = computed(() => injectedPresentationStyle.value)
</script>
