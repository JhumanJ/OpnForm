<template>
  <div
    :class="wrapperClass"
    :style="inputStyle"
  >
    <div class="flex justify-between">
      <slot name="status">
        <toggle-switch-input
          v-model="modelValue.status"
          name="status"
          label="Enabled"
        />
      </slot>
    </div>

    <slot />

    <slot name="logic">
      <div class="border-t mt-6 pt-6">
        <collapse
          v-model="showLogic"
          class="w-full"
        >
          <template #title>
            <div class="flex gap-x-3 items-start pr-8">
              <div
                class="transition-colors"
                :class="{
                  'text-blue-600': showLogic,
                  'text-neutral-300': !showLogic,
                }"
              >
                <Icon
                  name="material-symbols:settings"
                  size="30px"
                />
              </div>
              <div class="flex-grow">
                <h3 class="font-semibold">
                  Logic
                </h3>
                <p class="text-neutral-500 text-xs">
                  Only run integration when a condition is met
                </p>
              </div>
            </div>
          </template>
          <condition-editor
            ref="filter-editor"
            v-model="modelValue.logic"
            class="mt-4 border-t border rounded-md integration-logic"
            :form="form"
          />
        </collapse>
      </div>
    </slot>
  </div>
</template>

<script setup>
import ConditionEditor from "~/components/open/forms/components/form-logic-components/ConditionEditor.client.vue"
import Collapse from "~/components/app/Collapse.vue"

const props = defineProps({
  integration: { type: Object, required: true },
  modelValue: { type: Object, required: false },
  wrapperClass: { type: String, required: false },
  inputStyle: { type: Object, required: false },
  form: { type: Object, required: false },
})

defineEmits(["close"])
const showLogic = ref(!!props.modelValue.logic)
</script>

<style lang="scss">
.integration-logic {
  .query-builder-group__group-children {
    margin: 4px 0px 0px 0px !important;
  }
}
</style>
