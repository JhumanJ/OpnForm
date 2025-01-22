<template>
  <div
    v-if="field"
    class="py-2"
  >
    <div class="px-4">
      <text-input
        name="name"
        :form="field"
        wrapper-class="mb-2"
        :required="true"
        label="Block Name"
      />

      <HiddenRequiredDisabled
        :form="field"
        :field="field"
        :can-be-disabled="false"
        :can-be-hidden="true"
        :can-be-required="false"
      />
      
      <div class="grid grid-cols-2 gap-2 mt-2">
        <select-input
          name="width"
          class="flex-grow"
          :options="[
            { name: 'Full', value: 'full' },
            { name: '1/2', value: '1/2' },
            { name: '1/3', value: '1/3' },
            { name: '2/3', value: '2/3' },
            { name: '1/4', value: '1/4' },
            { name: '3/4', value: '3/4' },
          ]"
          :form="field"
          label="Width"
        />
        <select-input
          v-if="['nf-text', 'nf-image'].includes(field.type)"
          name="align"
          class="flex-grow"
          :options="[
            { name: 'Left', value: 'left' },
            { name: 'Center', value: 'center' },
            { name: 'Right', value: 'right' },
            { name: 'Justify', value: 'justify' },
          ]"
          :form="field"
          label="Alignment"
        />
      </div>
    </div>

    <div
      v-if="field.type == 'nf-text'"
      class="border-t mt-4"
    >
      <rich-text-area-input
        class="mx-4"
        name="content"
        :form="field"
        label="Content"
        :required="false"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-page-break'"
      class="border-b py-2 px-4"
    >
      <text-input
        name="next_btn_text"
        :form="field"
        label="Next button label"
        :required="true"
      />
      <text-input
        name="previous_btn_text"
        :form="field"
        label="Previous button label"
        help="Displayed on the next page"
        :required="true"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-image'"
      class="border-t mt-4"
    >
      <image-input
        name="image_block"
        class="mx-4"
        :form="field"
        label="Upload Image"
        :required="false"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-code'"
      class="border-t"
    >
      <code-input
        name="content"
        class="mt-4 mx-4"
        :form="field"
        label="Content"
        help="You can add any html code, including iframes"
      />
    </div>
  </div>
</template>

<script setup>
import HiddenRequiredDisabled from './HiddenRequiredDisabled.vue'

const props = defineProps({
  field: {
    type: Object,
    required: false
  },
  form: {
    type: Object,
    required: false
  }
})

watch(() => props.field?.width, (val) => {
  if (val === undefined || val === null) {
    props.field.width = 'full'
  }
}, { immediate: true })

watch(() => props.field?.align, (val) => {
  if (val === undefined || val === null) {
    props.field.align = 'left'
  }
}, { immediate: true })

onMounted(() => {
  if (props.field?.width === undefined || props.field?.width === null) {
    props.field.width = 'full'
  }
})
</script>
