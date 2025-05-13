<template>
  <form
    v-if="form"
    @submit.prevent=""
  >
    <FormProgressbar
      :form-manager="formManager"
      :theme="theme"
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
            draggingNewBlock ? 'rounded-md bg-blue-50 dark:bg-gray-800' : '',
          ]"
          ghost-class="ghost-item"
          filter=".not-draggable"
          :animation="200"
          :disabled="!allowDragging"
          @change="handleDragDropped"
        >
          <template #item="{element}">
            <open-form-field
              :field="element"
              :form-manager="formManager"
              :theme="theme"
            />
          </template>
        </draggable>
      </div>
    </transition>

    <!-- Replace Captcha with CaptchaWrapper -->
    <CaptchaWrapper
      v-if="form.use_captcha"
      :form-manager="formManager"
      :theme="theme"
    />

    <!--  Submit, Next and previous buttons  -->
    <div class="flex flex-wrap justify-center w-full">
      <open-form-button
        v-if="formPageIndex>0 && previousFieldsPageBreak"
        native-type="button"
        :color="form.color"
        :theme="theme"
        class="mt-2 px-8 mx-1"
        @click="handlePreviousClick"
      >
        {{ previousFieldsPageBreak.previous_btn_text }}
      </open-form-button>

      <slot
        v-if="isLastPage"
        name="submit-btn"
        :loading="form.busy"
      />
      <open-form-button
        v-else-if="currentFieldsPageBreak"
        native-type="button"
        :color="form.color"
        :theme="theme"
        class="mt-2 px-8 mx-1"
        :loading="form.busy"
        @click.stop="handleNextClick"
      >
        {{ currentFieldsPageBreak.next_btn_text }}
      </open-form-button>
      <div v-if="!currentFieldsPageBreak && !isLastPage">
        {{ $t('forms.wrong_form_structure') }}
      </div>
      <div
        v-if="hasPaymentBlock"
        class="mt-6 flex justify-center w-full"
      >
        <p class="text-xs text-gray-400 dark:text-gray-500 flex text-center max-w-md">
          {{ $t('forms.payment.payment_disclaimer') }}
        </p>
      </div>
    </div>
  </form>
</template>

<script setup>
import draggable from 'vuedraggable'
import OpenFormButton from './OpenFormButton.vue'
import CaptchaWrapper from '~/components/forms/components/CaptchaWrapper.vue'
import OpenFormField from './OpenFormField.vue'
import FormProgressbar from './FormProgressbar.vue'
import { useWorkingFormStore } from '~/stores/working_form'

const props = defineProps({
  formManager: { type: Object, required: true },
  theme: { type: Object, required: true }
})

const workingFormStore = useWorkingFormStore()

// Derive everything from formManager
const state = computed(() => props.formManager.state)
const form = computed(() => props.formManager.config.value)
const formPageIndex = computed(() => props.formManager.state.currentPage)
const strategy = computed(() => props.formManager.strategy.value)
const structure = computed(() => props.formManager.structure)

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

const handleNextClick = async () => {
  await props.formManager.nextPage()
  if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' })
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
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md;
}
</style>
