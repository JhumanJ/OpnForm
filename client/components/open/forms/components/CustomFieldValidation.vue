<template>
  <div class="py-2 px-4">
    <p class="text-gray-500 text-xs mb-3">
      Add some custom validation. Save your form before testing.
    </p>
    
    <div class="py-2">
      <p class="font-semibold text-sm text-gray-700">
        Validation criteria for field acceptance
      </p>
      <condition-editor
        ref="filter-editor"
        v-model="validation.conditions"
        class="mt-1 border-t border rounded-md mb-3"
        :form="form"
      />
      <text-input
        name="error_message"
        class=""
        :form="field.validation"
        label="Error message"
        help="Displayed when the validation fails"
      />
    </div>
  </div>
</template>

<script>
import ConditionEditor from "./form-logic-components/ConditionEditor.client.vue"
import { default as _has } from "lodash/has"
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
