<template>
  <form v-if="form" @submit.prevent="">
    <FormProgressbar :form-manager="formManager" />

    <SlidingTransition direction="vertical" :step="currentIndex">
      <div :key="currentIndex" class="w-full">
        <div class="grid grid-cols-12 gap-2">
          <div v-if="isBackgroundLayout" class="col-span-12 relative">
            <div class="absolute inset-0 -z-10">
              <BlockMediaLayout :image="currentMedia" />
            </div>
            <div class="relative">
              <BlockRenderer :block="currentBlock" :form-manager="formManager" />
            </div>
          </div>

          <template v-else-if="isSplitLayout">
            <div :class="splitLeftCol" class="px-2">
              <BlockMediaLayout :image="currentMedia" />
            </div>
            <div :class="splitRightCol" class="px-2">
              <BlockRenderer :block="currentBlock" :form-manager="formManager" />
            </div>
          </template>

          <template v-else-if="isSideSmall">
            <div :class="sideSmallLeftCol" class="px-2">
              <BlockMediaLayout :image="currentMedia" />
            </div>
            <div :class="sideSmallRightCol" class="px-2">
              <BlockRenderer :block="currentBlock" :form-manager="formManager" />
            </div>
          </template>

          <template v-else>
            <div class="col-span-12">
              <BlockRenderer :block="currentBlock" :form-manager="formManager" />
            </div>
          </template>
        </div>
      </div>
    </SlidingTransition>

    <div class="flex flex-wrap justify-center w-full">
      <open-form-button
        v-if="currentIndex>0"
        native-type="button"
        :form="form"
        class="mt-2 px-8 mx-1"
        @click.stop="handlePreviousClick"
      >
        {{ $t('forms.buttons.previous') }}
      </open-form-button>

      <template v-if="isLast">
        <slot name="submit-btn" :loading="isProcessing">
          <open-form-button
            :form="form"
            class="mt-2 px-8 mx-1"
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
        class="mt-2 px-8 mx-1"
        :loading="isProcessing"
        @click.stop="handleNextClick"
      >
        {{ currentBlock?.next_button_text || $t('forms.buttons.next') }}
      </open-form-button>
    </div>
  </form>
</template>

<script setup>
import BlockRenderer from './BlockRenderer.vue'
import BlockMediaLayout from './components/BlockMediaLayout.vue'
import FormProgressbar from './FormProgressbar.vue'
import OpenFormButton from './OpenFormButton.vue'
import SlidingTransition from '../../global/transitions/SlidingTransition.vue'

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

const isBackgroundLayout = computed(() => currentMedia.value && currentMedia.value.layout === 'background')
const isSplitLayout = computed(() => currentMedia.value && ['left-split','right-split'].includes(currentMedia.value.layout))
const isSideSmall = computed(() => currentMedia.value && ['left-small','right-small'].includes(currentMedia.value.layout))

const splitLeftCol = computed(() => currentMedia.value?.layout === 'left-split' ? 'col-span-6' : 'col-span-6 order-2')
const splitRightCol = computed(() => currentMedia.value?.layout === 'left-split' ? 'col-span-6' : 'col-span-6 order-1')

const sideSmallLeftCol = computed(() => currentMedia.value?.layout === 'left-small' ? 'col-span-3' : 'col-span-3 order-2')
const sideSmallRightCol = computed(() => currentMedia.value?.layout === 'left-small' ? 'col-span-9' : 'col-span-9 order-1')

const handlePreviousClick = () => {
  props.formManager.previousPage()
  if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleNextClick = () => {
  props.formManager.nextPage().then(() => {
    if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
  })
}
</script>


