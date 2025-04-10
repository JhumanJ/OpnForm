<template>
  <template v-if="form.show_progress_bar">
    <div
      v-if="isIframe"
      class="mb-4 p-2"
    >
      <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 relative border rounded-full overflow-hidden">
        <div
          class="h-full transition-all duration-300 rounded-r-full"
          :class="{ 'w-0': formProgress === 0 }"
          :style="{ width: formProgress + '%', background: form.color }"
        />
      </div>
    </div>
    <div
      v-else
      class="fixed top-0 left-0 right-0 z-50"
    >
      <div class="w-full h-[0.2rem] bg-gray-200 dark:bg-gray-600 relative overflow-hidden">
        <div
          class="h-full transition-all duration-300"
          :class="{ 'w-0': formProgress === 0 }"
          :style="{ width: formProgress + '%', background: form.color }"
        />
      </div>
    </div>
  </template>
</template>

<script setup>
import { useIsIframe } from '~/composables/useIsIframe'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  fields: {
    type: Array,
    required: true
  },
  formData: {
    type: Object,
    required: true
  }
})

const isIframe = useIsIframe()

const formProgress = computed(() => {
  const requiredFields = props.fields.filter(field => field.required)
  if (requiredFields.length === 0) {
    return 100
  }
  const completedFields = requiredFields.filter(field => ![null, undefined, ''].includes(props.formData[field.id]))
  const progress = (completedFields.length / requiredFields.length) * 100
  return Math.round(progress)
})
</script> 