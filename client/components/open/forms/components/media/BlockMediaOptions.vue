<template>
  <div v-if="isFocusedMode" class="px-4 mt-6">
    <EditorSectionHeader icon="i-heroicons-photo" title="Media" />

    <image-input
      name="image.url"
      :form="model"
      label="Image"
      :required="false"
      class="mt-2"
    />

    <template v-if="model?.image && model.image.url">
      <text-input
        :form="model"
        name="image.alt"
        label="Alt text"
        placeholder="Describe the image for accessibility (max 125 characters)"
        :max-char-limit="125"
        :show-char-limit="true"
        class="mt-3"
      />

      <OptionSelectorInput
        v-model="model.image.layout"
        name="image.layout"
        :form="model"
        label="Layout"
        :options="layoutOptions"
        :multiple="false"
        :columns="2"
      />

      <div class="mt-4">
        <FocalPointPicker label="Focal point" v-model="model.image.focal_point" :src="model.image.url" />
      </div>

      <div class="mt-4">
        <SliderInput
          v-model="model.image.brightness"
          name="image.brightness"
          :form="model"
          :min-slider="-100"
          :max-slider="100"
          :step-slider="1"
          label="Brightness"
        />
      </div>
    </template>
  </div>
</template>

<script setup>
import EditorSectionHeader from '~/components/open/forms/components/form-components/EditorSectionHeader.vue'
import FocalPointPicker from './FocalPointPicker.vue'
import SliderInput from '~/components/forms/core/SliderInput.vue'

const props = defineProps({
  model: { type: Object, required: true },
  form: { type: Object, required: false }
})

const isFocusedMode = computed(() => (props.form?.presentation_style || 'classic') === 'focused')

const layoutOptions = [
  { name: 'between', label: 'Between' },
  { name: 'background', label: 'Background (full)' },
  { name: 'left-small', label: 'Left side (small)' },
  { name: 'right-small', label: 'Right side (small)' },
  { name: 'left-split', label: 'Left side (50% split)' },
  { name: 'right-split', label: 'Right side (50% split)' },
]

function setDefaultImageSettings() {
  if (!props.model.image) props.model.image = {}
  props.model.image.layout = props.model.image.layout ?? 'between'
  props.model.image.focal_point = props.model.image.focal_point ?? { x: 50, y: 50 }
  props.model.image.brightness = typeof props.model.image.brightness === 'number' ? props.model.image.brightness : 0
}

function clearImageSettings() {
  if (!props.model.image) return
  props.model.image.alt = null
  props.model.image.layout = null
  props.model.image.focal_point = null
  props.model.image.brightness = 0
}

watch(() => props.model?.image?.url, (val) => {
  if (val) {
    setDefaultImageSettings()
  } else {
    clearImageSettings()
  }
})
</script>


