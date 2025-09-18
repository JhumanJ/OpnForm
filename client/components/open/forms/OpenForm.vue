<template>
  <form
    v-if="form"
    @submit.prevent=""
  >
    <FormProgressbar
      :form-manager="formManager"
    />
    <transition
      name="fade"
      mode="out-in"
    >
      <div
        :key="formPageIndex"
        class="form-group flex flex-wrap w-full"
      >
        <draggable
          :list="currentFields"
          group="form-elements"
          item-key="id"
          class="grid grid-cols-12 relative transition-all w-full"
          :class="[
            draggingNewBlock ? 'rounded-md bg-blue-50 dark:bg-neutral-800' : '',
          ]"
          ghost-class="ghost-item"
          filter=".not-draggable"
          :animation="200"
          :disabled="!allowDragging"
          @change="handleDragDropped"
        >
          <template #item="{element}">
            <VTransition name="fadeHeight">
              <open-form-field
                :field="element"
                :form-manager="formManager"
              />
            </VTransition>
          </template>
        </draggable>
      </div>
    </transition>

    <!-- Replace Captcha with CaptchaWrapper -->
    <CaptchaWrapper
      v-if="form.use_captcha"
      :form-manager="formManager"
    />

    <!--  Submit, Next and previous buttons  -->
    <div class="flex flex-wrap justify-center w-full">
      <open-form-button
        v-if="formPageIndex>0 && previousFieldsPageBreak"
        native-type="button"
        :form="form"
        class="mt-2 px-8 mx-1"
        @click.stop="handlePreviousClick"
      >
        {{ previousFieldsPageBreak.previous_btn_text || $t('forms.buttons.previous') }}
      </open-form-button>

      <template v-if="isLastPage">
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
        v-else-if="currentFieldsPageBreak"
        native-type="button"
        :form="form"
        class="mt-2 px-8 mx-1"
        :loading="isProcessing"
        @click.stop="handleNextClick"
      >
        {{ currentFieldsPageBreak.next_btn_text || $t('forms.buttons.next') }}
      </open-form-button>
      <div v-if="structure && !currentFieldsPageBreak && !isLastPage">
        {{ $t('forms.wrong_form_structure') }}
      </div>
      <div
        v-if="hasPaymentBlock"
        class="mt-6 flex justify-center w-full"
      >
        <p class="text-xs text-neutral-400 dark:text-neutral-500 flex text-center max-w-md">
          {{ $t('forms.payment.payment_disclaimer') }}
        </p>
      </div>
    </div>
  </form>
</template>

<script setup>
import draggable from 'vuedraggable'
import OpenFormButton from './OpenFormButton.vue'
import CaptchaWrapper from '~/components/forms/heavy/components/CaptchaWrapper.vue'
import OpenFormField from './OpenFormField.vue'
import FormProgressbar from './FormProgressbar.vue'
import { useWorkingFormStore } from '~/stores/working_form'

const props = defineProps({
  formManager: { type: Object, required: true }
})

const emit = defineEmits(['submit'])

const workingFormStore = useWorkingFormStore()

// Derive everything from formManager
const state = computed(() => props.formManager.state)
const form = computed(() => props.formManager.config.value)
const formPageIndex = computed(() => props.formManager.state.currentPage)
const strategy = computed(() => props.formManager.strategy.value)
const structure = props.formManager.structure

// Provide theme context for all child form components
provide('formTheme', computed(() => form.value?.theme || 'default'))
provide('formSize', computed(() => form.value?.size || 'md'))  
provide('formBorderRadius', computed(() => form.value?.border_radius || 'small'))

const hasPaymentBlock = computed(() => structure.value?.currentPageHasPaymentBlock?.value ?? false)

const currentFields = computed(() => structure.value?.getPageFields?.(state.value.currentPage) ?? [])

const isLastPage = computed(() => {
  const s = structure.value
  if (!s) return true
  if (s.isLastPage && 'value' in s.isLastPage) return s.isLastPage.value
  if (s.pageCount && 'value' in s.pageCount) {
    const count = s.pageCount.value || 0
    return state.value.currentPage >= Math.max(0, count - 1)
  }
  return true
})

const currentFieldsPageBreak = computed(() => structure.value?.currentPageBreak?.value ?? null)
const previousFieldsPageBreak = computed(() => structure.value?.previousPageBreak?.value ?? null)

const allowDragging = computed(() => strategy.value.admin.allowDragging)
const draggingNewBlock = computed(() => workingFormStore.draggingNewBlock)

const handlePreviousClick = () => {
  props.formManager.previousPage()
  if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleNextClick = () => {
  props.formManager.nextPage().then(() => {
    if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
  })
}

const handleDragDropped = (data) => {
  if (!structure.value) return

  const getAbsoluteIndex = (relativeIndex) => {
    return structure.value.getTargetDropIndex(relativeIndex, state.value.currentPage)
  }

  if (data.added) {
    const targetIndex = getAbsoluteIndex(data.added.newIndex)
    workingFormStore.addBlock(data.added.element, targetIndex, false)
  }
  if (data.moved) {
    const oldTargetIndex = getAbsoluteIndex(data.moved.oldIndex)
    const newTargetIndex = getAbsoluteIndex(data.moved.newIndex)
    workingFormStore.moveField(oldTargetIndex, newTargetIndex)
  }
}

const isProcessing = computed(() => props.formManager.state.isProcessing)
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md;
}
</style>
