<template>
  <div
    v-if="logic"
    :key="resetKey"
  >
    <p class="text-gray-400 text-xs mb-3">
      Select a field, add some conditions, and finally add some actions.
    </p>
    <div class="relative flex">
      <UButtonGroup size="xs">
        <UButton
          color="gray"
          icon="i-heroicons-arrow-down-on-square"
          @click="showCopyFormModal = true"
        >
          Copy from
        </UButton>
        <UButton
          color="gray"
          icon="i-heroicons-arrow-up-on-square"
          @click="showCopyToModal = true"
        >
          Copy to
        </UButton>
        <UButton
          color="gray"
          icon="i-mdi-clear-outline"
          @click="clearAll"
        >
          Clear
        </UButton>
      </UButtonGroup>
    </div>

    <h5 class="font-medium text-gray-700 mt-3">
      1. Conditions
    </h5>
    <condition-editor
      ref="filter-editor"
      v-model="logic.conditions"
      class="mt-1 border-t border rounded-md"
      :form="form"
    />

    <h5 class="font-medium text-gray-700 mt-3">
      2. Actions
    </h5>
    <p class="text-gray-500 text-xs mb-3">
      Action(s) triggered when above conditions are true.
    </p>
    <flat-select-input
      :key="resetKey"
      v-model="logic.actions"
      name="actions"
      :multiple="true"
      class="mt-1"
      placeholder="Actions..."
      :options="actionOptions"
      @update:model-value="onActionInput"
    />

    <p class="text-gray-500 text-xs mb-3">
      Note that hidden fields can never be required.
    </p>

    <modal
      max-width="sm"
      
      :show="showCopyFormModal"
      @close="showCopyFormModal = false"
    >
      <h3 class="font-semibold block text-lg">
        Copy logic from another field
      </h3>
      <p class="text-gray-400 text-xs mb-5">
        Select another field/block to copy its logic and apply it to "{{
          field.name
        }}".
      </p>
      <select-input
        v-model="copyFrom"
        name="copy_from"
        emit-key="value"
        label="Copy logic from"
        placeholder="Choose a field/block..."
        :options="copyFromOptions"
        :searchable="copyFromOptions && copyFromOptions.options > 5"
      />
      <div class="flex justify-between mt-2">
        <UButton
          color="primary"
          icon="i-heroicons-check"
          @click="copyLogic"
        >
          Confirm & Copy
        </UButton>
        <UButton
          color="gray"
          icon="i-heroicons-x-mark"
          class="ml-1"
          @click="showCopyFormModal = false"
        >
          Close
        </UButton>
      </div>
    </modal>


    <modal
      max-width="sm"
      :show="showCopyToModal"
      @close="showCopyToModal = false"
    >
      <h3 class="font-semibold block text-lg">
        Copy logic to other fields
      </h3>
      <p class="text-gray-400 text-xs mb-5">
        Select fields to copy the logic from "{{ field.name }}" to them.
      </p>
      <select-input
        v-model="copyTo"
        name="copy_to"
        emit-key="value"
        label="Copy logic to"
        placeholder="Choose fields..."
        :options="copyToOptions"
        :multiple="true"
        :searchable="copyToOptions && copyToOptions.length > 5"
      />
      <div class="flex justify-between mt-2">
        <UButton
          color="primary"
          icon="i-heroicons-check"
          @click="copyLogicToFields"
        >
          Confirm & Copy
        </UButton>
        <UButton
          color="gray"
          icon="i-heroicons-x-mark"
          class="ml-1"
          @click="showCopyToModal = false"
        >
          Close
        </UButton>
      </div>
    </modal>
  </div>
</template>

<script>
import ConditionEditor from "./ConditionEditor.client.vue"
import Modal from "../../../../global/Modal.vue"
import clonedeep from "clone-deep"
import { default as _has } from "lodash/has"

export default {
  name: "FormBlockLogicEditor",
  components: { Modal, ConditionEditor },
  props: {
    field: {
      type: Object,
      required: false,
    },
    form: {
      type: Object,
      required: false,
    },
  },

  data() {
    return {
      resetKey: 0,
      logic: this.field.logic || {
        conditions: null,
        actions: [],
      },
      showCopyFormModal: false,
      copyFrom: null,
      showCopyToModal: false,
      copyTo: [],
    }
  },

  computed: {
    copyFromOptions() {
      return this.form.properties
        .filter((field) => {
          return (
            field.id !== this.field.id &&
            _has(field, "logic") &&
            field.logic !== null &&
            Object.keys(field.logic || {}).length > 0
          )
        })
        .map((field) => {
          return { name: field.name, value: field.id }
        })
    },
    copyToOptions() {
      return this.form.properties
        .filter((field) => {
          return field.id !== this.field.id
        })
        .map((field) => {
          return { name: field.name, value: field.id }
        })
    },
    actionOptions() {
      if (
        [
          "nf-text",
          "nf-code",
          "nf-page-break",
          "nf-divider",
          "nf-image",
        ].includes(this.field.type)
      ) {
        if (this.field.hidden) {
          return [{ name: "Show Block", value: "show-block" }]
        } else {
          return [{ name: "Hide Block", value: "hide-block" }]
        }
      }

      if (this.field.hidden) {
        return [
          { name: "Show Block", value: "show-block" },
          { name: "Require answer", value: "require-answer" },
        ]
      } else if (this.field.disabled) {
        return [
          { name: "Enable Block", value: "enable-block" },
          this.field.required
            ? { name: "Make it optional", value: "make-it-optional" }
            : {
                name: "Require answer",
                value: "require-answer",
              },
        ]
      } else {
        return [
          { name: "Hide Block", value: "hide-block" },
          { name: "Disable Block", value: "disable-block" },
          this.field.required
            ? { name: "Make it optional", value: "make-it-optional" }
            : {
                name: "Require answer",
                value: "require-answer",
              },
        ]
      }
    },
  },

  watch: {
    logic: {
      handler() {
        this.field.logic = this.logic
      },
      deep: true,
    },
    "field.id": {
      handler() {
        // On field change, reset logic
        this.logic = this.field.logic || {
          conditions: null,
          actions: [],
        }
      },
    },
    "field.required": "cleanConditions",
    "field.disabled": "cleanConditions",
    "field.hidden": "cleanConditions",
  },

  mounted() {
    if (!_has(this.field, "logic")) {
      this.field.logic = this.logic
    }
  },

  methods: {
    clearAll() {
      this.logic.conditions = null
      this.logic.actions = []
      this.refreshActions()
    },
    onActionInput() {
      if (this.logic.actions.length >= 2) {
        if (
          this.logic.actions[1] === "require-answer" &&
          this.logic.actions[0] === "hide-block"
        ) {
          this.logic.actions = ["require-answer"]
        } else if (
          this.logic.actions[1] === "hide-block" &&
          this.logic.actions[0] === "require-answer"
        ) {
          this.logic.actions = ["hide-block"]
        }
        this.refreshActions()
      }
    },
    cleanConditions() {
      const availableActions = this.actionOptions.map(function (op) {
        return op.value
      })
      this.logic.actions = availableActions.filter((value) =>
        this.logic.actions.includes(value),
      )
      this.refreshActions()
    },
    refreshActions() {
      this.resetKey++
    },
    copyLogic() {
      if (this.copyFrom) {
        const property = this.form.properties.find((property) => {
          return property.id === this.copyFrom
        })
        if (property && property.logic) {
          this.logic = clonedeep(property.logic)
          this.cleanConditions()
        }
      }
      this.showCopyFormModal = false
    },
    copyLogicToFields() {
      if (this.copyTo.length) {
        this.copyTo.forEach((fieldId) => {
          const targetField = this.form.properties.find(
            (property) => property.id === fieldId
          )
          if (targetField) {
            targetField.logic = clonedeep(this.logic)
          }
        })
      }
      this.showCopyToModal = false
      this.copyTo = []
    },
  },
}
</script>
