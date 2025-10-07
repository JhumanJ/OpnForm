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
        <VueDraggable
          :model-value="currentFields"
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
          @add="handleDragAdd"
          @update="handleDragUpdate"
        >
          <template #default>
            <VTransition
              v-for="element in currentFields"
              :key="element.id"
              name="fadeHeight"
            >
              <open-form-field
                :field="element"
                :form-manager="formManager"
              />
            </VTransition>
          </template>
        </VueDraggable>
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

      <slot
        v-if="isLastPage"
        name="submit-btn"
        :loading="isProcessing"
      />
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
      <div v-if="!currentFieldsPageBreak && !isLastPage">
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
import { VueDraggable } from 'vue-draggable-plus'
import OpenFormButton from './OpenFormButton.vue'
import CaptchaWrapper from '~/components/forms/heavy/components/CaptchaWrapper.vue'
import OpenFormField from './OpenFormField.vue'
import FormProgressbar from './FormProgressbar.vue'
import { useWorkingFormStore } from '~/stores/working_form'

const props = defineProps({
  formManager: { type: Object, required: true }
})

const workingFormStore = useWorkingFormStore()

// Derive everything from formManager
const state = computed(() => props.formManager.state)
const form = computed(() => props.formManager.config.value)
const formPageIndex = computed(() => props.formManager.state.currentPage)
const strategy = computed(() => props.formManager.strategy.value)
const structure = computed(() => props.formManager.structure)

// Provide theme context for all child form components
provide('formTheme', computed(() => form.value?.theme || 'default'))
provide('formSize', computed(() => form.value?.size || 'md'))  
provide('formBorderRadius', computed(() => form.value?.border_radius || 'small'))

const hasPaymentBlock = computed(() => {
  return structure.value?.currentPageHasPaymentBlock.value ?? false
})

const currentFields = computed(() => {
  return structure.value?.getPageFields(state.value.currentPage) ?? []
})

const isLastPage = computed(() => {
  const result = structure.value?.isLastPage.value ?? true
  return result
})

const currentFieldsPageBreak = computed(() => 
  structure.value?.currentPageBreak.value
)
const previousFieldsPageBreak = computed(() => 
  structure.value?.previousPageBreak.value
)

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

const getAbsoluteIndex = (relativeIndex) => {
  return structure.value.getTargetDropIndex(relativeIndex, state.value.currentPage)
}

const handleDragAdd = (evt) => {
  if (!structure.value) return
  const targetIndex = getAbsoluteIndex(evt.newIndex)
  const payload = evt?.clonedData
  workingFormStore.addBlock(payload, targetIndex, false)
}

const handleDragUpdate = (evt) => {
  if (!structure.value) return
  const oldTargetIndex = getAbsoluteIndex(evt.oldIndex)
  const newTargetIndex = getAbsoluteIndex(evt.newIndex)
  if (oldTargetIndex !== newTargetIndex) {
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
