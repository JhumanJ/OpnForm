<template>
  <div v-if="isFocusedMode" class="px-4 mt-6">
    <EditorSectionHeader icon="i-heroicons-photo" title="Media & Layout" />

    <!-- Unified image input with settings popover (focus/brightness/alt) -->
    <ImageWithSettings :form="model" name="image.url" label="Image" kind="block" />

    <!-- Layout selection appears only when an image is present -->
    <template v-if="model?.image && model.image.url">
      <OptionSelectorInput
        v-model="model.image.layout"
        name="image.layout"
        :form="model"
        label="Layout"
        :options="layoutOptions"
        :multiple="false"
        :columns="3"
      />
    </template>
  </div>
</template>

<script setup>
import EditorSectionHeader from '~/components/open/forms/components/form-components/EditorSectionHeader.vue'
import ImageWithSettings from './ImageWithSettings.vue'

const props = defineProps({
  model: { type: Object, required: true },
  form: { type: Object, required: false }
})

const isFocusedMode = computed(() => (props.form?.presentation_style || 'classic') === 'focused')

const layoutOptions = [
  { name: 'between', icon: 'i-opnform-form-layout-between', iconClass: 'w-[70px] h-[50px] rounded text-slate-600' },
  { name: 'left-small', icon: 'i-opnform-form-layout-left-small', iconClass: 'w-[70px] h-[50px] rounded' },
  { name: 'left-split', icon: 'i-opnform-form-layout-left-split', iconClass: 'w-[70px] h-[50px] rounded' },
  { name: 'background', icon: 'i-opnform-form-layout-background', iconClass: 'w-[70px] h-[50px] rounded' },
  { name: 'right-small', icon: 'i-opnform-form-layout-right-small', iconClass: 'w-[70px] h-[50px] rounded' },
  { name: 'right-split', icon: 'i-opnform-form-layout-right-split', iconClass: 'w-[70px] h-[50px] rounded' },
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


