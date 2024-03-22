<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="title">
      <a class="cursor-pointer" @click.prevent="openHelp">Need help with this integration?</a>
    </slot>

    <slot name="status">
      <div class="my-4">
        <toggle-switch-input name="status" v-model="modelValue.status" class="mt-4" label="Status"
          help="Only run integration when a status is enabled" />
      </div>
    </slot>

    <slot />

    <slot name="logic">
      <collapse class="my-5 w-full border-b" v-model="showLogic">
        <template #title>
          <div class="flex items-center pr-8">
            <div class="mr-1 mb-3" :class="{ 'text-blue-600': showLogic, 'text-gray-500': !showLogic }">
              <Icon name="material-symbols:settings" size="30px" />
            </div>
            <h3 class="font-semibold flex-grow">
              Logic
              <p class="text-gray-400 text-xs mb-3">
                Only run integration when a condition is met
              </p>
            </h3>
          </div>
        </template>
        <condition-editor ref="filter-editor" v-model="modelValue.logic" class="mt-1 border-t border rounded-md"
          :form="form" />
      </collapse>
    </slot>
  </div>
</template>

<script setup>
import ConditionEditor from '~/components/open/forms/components/form-logic-components/ConditionEditor.client.vue'

const props = defineProps({
  integration: { type: Object, required: true },
  modelValue: { required: false },
  wrapperClass: { type: String, required: false },
  inputStyle: { type: Object, required: false },
  form: { type: Object, required: false }
})

const crisp = useCrisp()
const emit = defineEmits(['close'])
let showLogic = ref(props.modelValue.logic ? true : false)

const openHelp = () => {
  if (props.integration && props.integration?.crisp_help_page_slug) {
    crisp.openHelpdeskArticle(props.integration?.crisp_help_page_slug)
    return
  }
  crisp.openHelpdesk()
}
</script>
