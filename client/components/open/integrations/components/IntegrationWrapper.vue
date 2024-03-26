<template>
  <div :class="wrapperClass" :style="inputStyle">
    <div class="flex justify-between">
      <slot name="status">
        <toggle-switch-input name="status" v-model="modelValue.status" label="Enabled"/>
      </slot>
      <slot name="help">
        <v-button class="flex" color="white" size="small" @click="openHelp">
          <Icon name="heroicons:question-mark-circle-16-solid" class="w-4 h-4 text-gray-500 -mt-[3px]"/>
          <span class="text-gray-500">
          Help
            </span>
        </v-button>
      </slot>
    </div>

    <slot/>

    <slot name="logic">
      <div class="-mx-6 px-6 border-t pt-6">
        <collapse class="w-full" v-model="showLogic">
          <template #title>
            <div class="flex gap-x-3 items-start pr-8">
              <div class="transition-colors" :class="{ 'text-blue-600': showLogic, 'text-gray-300': !showLogic }">
                <Icon name="material-symbols:settings" size="30px"/>
              </div>
              <div class="flex-grow">
                <h3 class="font-semibold">
                  Logic
                </h3>
                <p class="text-gray-400 text-xs">
                  Only run integration when a condition is met
                </p>
              </div>
            </div>
          </template>
          <condition-editor ref="filter-editor" v-model="modelValue.logic" class="mt-4 border-t border rounded-md integration-logic"
                            :form="form"/>
        </collapse>
      </div>
    </slot>
  </div>
</template>

<script setup>
import ConditionEditor from '~/components/open/forms/components/form-logic-components/ConditionEditor.client.vue'

const props = defineProps({
  integration: {type: Object, required: true},
  modelValue: {required: false},
  wrapperClass: {type: String, required: false},
  inputStyle: {type: Object, required: false},
  form: {type: Object, required: false}
})

const crisp = useCrisp()
const emit = defineEmits(['close'])
const showLogic = ref(!!props.modelValue.logic)

const openHelp = () => {
  if (props.integration && props.integration?.crisp_help_page_slug) {
    crisp.openHelpdeskArticle(props.integration?.crisp_help_page_slug)
    return
  }
  crisp.openHelpdesk()
}
</script>

<style lang="scss">
.integration-logic {
  .query-builder-group__group-children {
    margin: 4px 0px 0px 0px !important;
  }
}
</style>
