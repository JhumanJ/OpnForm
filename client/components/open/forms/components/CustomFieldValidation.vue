<template>
  <div class="py-2 px-4">
    <div class="space-y-4">
      <!-- Step 1: Validation Rules -->
      <div>
        <h3 class="font-medium text-gray-900 text-sm mb-1">
          Step 1: Set Validation Rules
        </h3>
        <p class="text-gray-500 text-xs mb-3">
          Define <span class="font-semibold">conditions that must be met</span> for this field to be valid. If these conditions are not met, the field will be marked as invalid.
        </p>
        <condition-editor
          ref="filter-editor"
          v-model="validation.conditions"
          class="mt-1 border-t border rounded-md"
          :form="form"
          :custom-validation="true"
        />
      </div>

      <!-- Step 2: Error Message -->
      <div>
        <h3 class="font-medium text-gray-900 text-sm mb-1">
          Step 2: Set Error Message
        </h3>
        <p class="text-gray-500 text-xs mb-2">
          Enter the message that will be <span class="font-semibold">shown to users when the validation rules above are not met</span>.
        </p>
        <text-input
          name="error_message"
          :form="field.validation"
          label="Error message"
        />
      </div>

      <UAlert
        icon="i-heroicons-information-circle"
        color="yellow"
        variant="subtle"
        size="sm"
        description="Remember to save your form to apply these validation rules."
      />
    </div>
  </div>
</template>

<script>
import { default as _has } from "lodash/has"
import ConditionEditor from "./form-logic-components/ConditionEditor.client.vue"
export default {
    name: 'FormValidation',
    components: {ConditionEditor},
    props: {
        field: {
            type: Object,
            required: false
        },
        form: {
            type: Object,
            required: false
        }
    },
    data() {
        return {
            show: false,
            validation: this.field.validation?.error_conditions || {
                conditions: null,
                actions: [],
            },
            error_message: this.field.validation?.error_message || ''
        }
    },

    watch: {
        logic: {
            handler() {
                this.field.validation.error_conditions = this.validation
            },
            deep: true,
        },
        "field.id": {
            handler() {
                // On field change, reset validation
                this.validation = this.field?.validation?.error_conditions || {
                    conditions: null,
                    actions: [],
                }
            },
        },
    },
    mounted() {
        if (!_has(this.field, "validation")) {
            this.field.validation = {
                error_conditions: this.validation,
                error_message: this.error_message }
        }
    },
    methods:{}
}
</script>
