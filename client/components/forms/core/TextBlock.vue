<template>
  <div
    class="text-block"
    :class="classes"
    v-html="processedContent"
  />
</template>

<script setup>
import { useParseMention } from '@/composables/components/useParseMention'
import { tv } from 'tailwind-variants'
import { textBlockTheme } from '~/lib/forms/themes/text-block.theme.js'

const props = defineProps({
  content: { type: String, required: true },
  mentionsAllowed: { type: Boolean, default: false },
  form: { type: Object, default: null },
  formData: { type: Object, default: null },
  size: { type: String, default: null },
})

const processedContent = computed(() => {
  return useParseMention(props.content, props.mentionsAllowed, props.form, props.formData)
})

const injectedSize = inject('formSize', null)
const resolvedSize = computed(() => props.size || injectedSize?.value || 'md')
const variants = computed(() => tv(textBlockTheme))
const classes = computed(() => variants.value({ size: resolvedSize.value }).root())
</script>
