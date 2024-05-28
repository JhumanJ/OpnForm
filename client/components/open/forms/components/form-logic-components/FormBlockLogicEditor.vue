<template>
  <div
    v-if="logic"
    :key="resetKey"
  >
    <h3 class="font-semibold block text-lg">
      Logic
    </h3>
    <p class="text-gray-400 text-xs mb-3">
      Add some logic to this block. Start by adding some conditions, and then
      add some actions.
    </p>
    <div class="relative flex">
      <div>
        <v-button
          color="light-gray"
          size="small"
          @click="showCopyFormModal = true"
        >
          <Icon name="lucide:copy" class="w-4 h-4 text-blue-600 inline mr-1 -mt-1" />
          Copy from
        </v-button>
      </div>
      <div>
        <v-button
          color="light-gray"
          shade="light"
          size="small"
          class="ml-1"
          @click="clearAll"
        >
          <Icon name="mdi:clear-outline" class="w-4 h-4 text-red-600 inline mr-1 -mt-1" />
          Clear All
        </v-button>
      </div>
    </div>

    <h5 class="font-semibold mt-3">
      1. Conditions
    </h5>
    <condition-editor
      ref="filter-editor"
      v-model="logic.conditions"
      class="mt-1 border-t border rounded-md"
      :form="form"
    />

    <h5 class="font-semibold mt-3">
      2. Actions
    </h5>
    <flat-select-input
      :key="resetKey"
      v-model="logic.actions"
      name="actions"
      :multiple="true"
      class="mt-1"
      placeholder="Actions..."
      help="Action(s) triggered when above conditions are true"
      :options="actionOptions"
      @update:model-value="onActionInput"
    />

    <modal
      :show="showCopyFormModal"
      @close="showCopyFormModal = false"
    >
      <div class="min-h-[450px]">
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
        <div class="flex justify-between mb-6">
          <v-button
            color="blue"
            shade="light"
            @click="copyLogic"
          >
            Confirm & Copy
          </v-button>
          <v-button
            color="gray"
            shade="light"
            class="ml-1"
            @click="showCopyFormModal = false"
          >
            Close
          </v-button>
        </div>
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
            field.logic !== {}
          )
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
  },
}
</script>
