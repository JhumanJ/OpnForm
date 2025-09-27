<template>
  <form v-if="form" @submit.prevent="" class="w-full relative overflow-hidden flex flex-col min-h-full">
    <!-- Fixed fullscreen background from form cover -->
    <div v-if="form.cover_picture" class="absolute inset-0 pointer-events-none">
      <BlockMediaLayout :image="coverMedia" alt="Form cover image" />
    </div>

    <!-- Fixed logo in top-left -->
    <div v-if="form.logo_picture" class="absolute top-10 left-10 z-10">
      <img :src="form.logo_picture" :alt="form.seo_meta?.site_name ? `${form.seo_meta.site_name} logo` : 'Form logo'" class="size-16 object-contain">
    </div>

    <!-- Alerts slot (renderer decides placement) -->
    <slot name="alerts" />

    <!-- Progressbar -->
    <FormProgressbar :form-manager="formManager" />

    <!-- Slide: question vertically centered, next/submit button slides with it -->
    <SlidingTransition 
      direction="vertical"
      :step="currentIndex"
      :auto-height="false"
      class="grow min-h-0 flex"
      :speed="500"
    >
      <div :key="currentIndex" class="w-full flex items-center px-6 grow min-h-0 z-10 h-full">
        <div class="w-full max-w-2xl mx-auto">
          <div class="relative">
            <!-- If current block defines background layout, render as underlay -->
            <div v-if="isBackgroundLayout" class="absolute inset-0 -z-10 pointer-events-none">
              <BlockMediaLayout :image="currentMedia" />
            </div>
            <BlockRenderer :block="currentBlock" :form-manager="formManager" />
          </div>

          <div class="mt-4">
            <template v-if="isLast">
              <slot name="submit-btn" :loading="isProcessing">
                <open-form-button
                  :form="form"
                  class="mt-2 px-6"
                  :loading="isProcessing"
                  @click.prevent="emit('submit')"
                >
                  {{ form.submit_button_text || $t('forms.buttons.submit') }}
                </open-form-button>
              </slot>
            </template>
            <open-form-button
              v-else
              native-type="button"
              :form="form"
              class="mt-2 px-6"
              :loading="isProcessing"
              @click.stop="handleNextClick"
            >
              {{ currentBlock?.next_button_text || $t('forms.buttons.next') }}
            </open-form-button>
          </div>
        </div>
      </div>
    </SlidingTransition>

    <!-- Cleanings slot -->
    <slot name="cleanings" />

    <!-- Captcha -->
    <CaptchaWrapper v-if="form.use_captcha" :form-manager="formManager" />

    <!-- Branding slot -->
    <slot name="branding" />
  </form>
</template>

<script setup>
import BlockRenderer from './BlockRenderer.vue'
import BlockMediaLayout from './components/BlockMediaLayout.vue'
import FormProgressbar from './FormProgressbar.vue'
import OpenFormButton from './OpenFormButton.vue'
import SlidingTransition from '../../global/transitions/SlidingTransition.vue'
import CaptchaWrapper from '~/components/forms/heavy/components/CaptchaWrapper.vue'

const props = defineProps({
  formManager: { type: Object, required: true }
})

const emit = defineEmits(['submit'])

const form = computed(() => props.formManager.config.value)
const structure = props.formManager.structure
const state = computed(() => props.formManager.state)

const currentIndex = computed(() => state.value.currentPage)
const currentFields = computed(() => structure?.value?.getPageFields
  ? structure.value.getPageFields(currentIndex.value)
  : [])
const currentBlock = computed(() => currentFields.value?.[0] || null)
const currentMedia = computed(() => currentBlock.value?.image || null)

const isLast = computed(() => structure?.value?.isLastPage?.value ?? false)
const isProcessing = computed(() => props.formManager.state.isProcessing)
// Reserved for future gating if focused renderer wants to branch
// const isSubmitted = computed(() => !!props.formManager?.state.isSubmitted)
// const isPasswordProtected = computed(() => !!form.value?.is_password_protected)

const isBackgroundLayout = computed(() => currentMedia.value && currentMedia.value.layout === 'background')

const handleNextClick = () => {
  props.formManager.nextPage().then(() => {
    if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
  })
}

const coverMedia = computed(() => ({
  url: form.value?.cover_picture,
  focal_point: form.value?.cover_settings?.focal_point,
  brightness: form.value?.cover_settings?.brightness
}))
</script>


