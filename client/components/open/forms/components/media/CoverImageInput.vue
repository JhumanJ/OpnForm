<template>
  <div>
    <image-input
      :form="form"
      name="cover_picture"
      :label="isFocused ? 'Background' : 'Cover (~1500px)'"
      :required="false"
    >
      <template #left-action>
        <UPopover
            arrow
          v-if="form?.cover_picture"
          :content="{ side: 'bottom', align: 'start' }"
        >
          <button
            class="text-neutral-500 hover:text-neutral-700 flex items-center mr-2"
            type="button"
            :aria-label="'Cover image settings'"
            @click.stop
          >
            <Icon name="heroicons:cog-6-tooth" class="h-5 w-5" />
          </button>

          <template #content>
            <div class="p-3 w-80">
              <FocalPointPicker name="cover_focal_point" label="Focal point" v-model="focalPoint" :src="form.cover_picture" />

              <div class="mt-4">
                <SliderInput
                  v-model="brightnessModel"
                  name="cover_picture_brightness"
                  :form="form"
                  :min-slider="-100"
                  :max-slider="100"
                  :step-slider="1"
                  label="Brightness"
                />
              </div>
            </div>
          </template>
        </UPopover>
      </template>
      
      <!-- Reset cover_settings when image cleared via parent ImageInput clearUrl -->
    </image-input>
  </div>
</template>

<script setup>
import FocalPointPicker from './FocalPointPicker.vue'
import SliderInput from '~/components/forms/core/SliderInput.vue'

const props = defineProps({
  form: { type: Object, required: true }
})

const isFocused = computed(() => (props.form?.presentation_style || 'classic') === 'focused')

const focalPoint = computed({
  get() {
    const s = props.form?.cover_settings || {}
    return s.focal_point ?? { x: 50, y: 50 }
  },
  set(val) {
    const base = props.form?.cover_settings ? { ...props.form.cover_settings } : {}
    base.focal_point = val
    props.form.cover_settings = base
  }
})

const brightnessModel = computed({
  get() {
    const s = props.form?.cover_settings
    if (s && typeof s.brightness === 'number') return s.brightness
    // Don't override existing; just default to 0 when missing
    return 0
  },
  set(val) {
    const base = props.form?.cover_settings ? { ...props.form.cover_settings } : {}
    base.brightness = typeof val === 'number' ? val : 0
    props.form.cover_settings = base
  }
})

watch(() => props.form?.cover_picture, (val) => {
  if (!val) {
    props.form.cover_settings = {}
  }
})
</script>


