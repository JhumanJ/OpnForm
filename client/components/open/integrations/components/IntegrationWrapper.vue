<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="title">
      <a class="cursor-pointer" @click.prevent="crisp.openHelpdesk()">Need help with this integration?</a>
    </slot>

    <slot name="status">
      <div class="my-4">
        <toggle-switch-input name="status" v-model="modelValue.status" class="mt-4" label="Status"
          help="Only run integration when a status is enabled" />
      </div>
    </slot>

    <slot />

    <slot name="logic">
      <div class="my-4">
        <h5 class="font-semibold mt-3">
          Logic
        </h5>
        <p class="text-gray-400 text-xs mb-3">
          Only run integration when a condition is met
        </p>
        <condition-editor ref="filter-editor" v-model="modelValue.logic" class="mt-1 border-t border rounded-md"
          :form="form" />
      </div>
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
</script>
