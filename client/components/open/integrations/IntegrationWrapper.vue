<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="title">
      <h3 class="font-semibold mt-4 text-xl">
        Connect your form to {{ service.name }}
        <pro-tag v-if="service?.is_pro === true" />
      </h3>
      <a class="cursor-pointer" @click.prevent="crisp.openHelpdesk()">Need help setting up?</a>
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

    <div class="flex">
      <slot name="back">
        <v-button class="mr-1" color="gray" :to="{ name: 'forms-slug-show-integrations' }">
          <svg class="inline w-3 h-3" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
          Back
        </v-button>
      </slot>
      <slot name="submit" class="flex-grow"></slot>
    </div>
  </div>
</template>

<script setup>
import ConditionEditor from '~/components/open/forms/components/form-logic-components/ConditionEditor.client.vue'

const props = defineProps({
  service: { type: Object, required: true },
  modelValue: { required: false },
  wrapperClass: { type: String, required: false },
  inputStyle: { type: Object, required: false },
  form: { type: Object, required: false }
})

const crisp = useCrisp()
// const emit = defineEmits(['update:modelValue'])

// const logic = props.modelValue.logic || ref({})
</script>
