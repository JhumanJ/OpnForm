<template>
  <div v-if="logic" :key="resetKey">
    <h3 class="font-semibold block text-lg">
      Logic
    </h3>
    <p class="text-gray-400 text-xs mb-3">
      Add some logic to this block. Start by adding some conditions, and then add some actions.
    </p>
    <div class="relative flex">
      <div>
        <v-button color="light-gray" size="small" @click="showCopyFormModal=true">
          <svg class="h-4 w-4 text-blue-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 15H4C3.46957 15 2.96086 14.7893 2.58579 14.4142C2.21071 14.0391 2 13.5304 2 13V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H13C13.5304 2 14.0391 2.21071 14.4142 2.58579C14.7893 2.96086 15 3.46957 15 4V5M11 9H20C21.1046 9 22 9.89543 22 11V20C22 21.1046 21.1046 22 20 22H11C9.89543 22 9 21.1046 9 20V11C9 9.89543 9.89543 9 11 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          Copy from
        </v-button>
      </div>
      <div>
        <v-button color="light-gray" shade="light" size="small" class="ml-1" @click="clearAll">
          <svg class="text-red-600 h-4 w-4 inline -mt-1 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 9L12 15M12 9L18 15M21 4H8L1 12L8 20H21C21.5304 20 22.0391 19.7893 22.4142 19.4142C22.7893 19.0391 23 18.5304 23 18V6C23 5.46957 22.7893 4.96086 22.4142 4.58579C22.0391 4.21071 21.5304 4 21 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>

          Clear All
        </v-button>
      </div>
    </div>

    <h5 class="font-semibold mt-3">
      1. Conditions
    </h5>
    <condition-editor ref="filter-editor" v-model="logic.conditions" class="mt-1 border-t border rounded-md" :form="form" />

    <h5 class="font-semibold mt-3">
      2. Actions
    </h5>
    <select-input :key="resetKey" v-model="logic.actions" name="actions"
                  :multiple="true" class="mt-1" placeholder="Actions..."
                  help="Action(s) triggered when above conditions are true"
                  :options="actionOptions"
                  @update:model-value="onActionInput"
    />

    <modal :show="showCopyFormModal" @close="showCopyFormModal = false">
      <div class="min-h-[450px]">
        <h3 class="font-semibold block text-lg">
          Copy logic from another field
        </h3>
        <p class="text-gray-400 text-xs mb-5">
          Select another field/block to copy its logic and apply it to "{{ field.name }}".
        </p>
        <select-input v-model="copyFrom" name="copy_from" emit-key="value"
                      label="Copy logic from" placeholder="Choose a field/block..."
                      :options="copyFromOptions" :searchable="copyFromOptions && copyFromOptions.options > 5"
        />
        <div class="flex justify-between mb-6">
          <v-button color="blue" shade="light" @click="copyLogic">
            Confirm & Copy
          </v-button>
          <v-button color="gray" shade="light" class="ml-1" @click="showCopyFormModal=false">
            Close
          </v-button>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import ConditionEditor from './ConditionEditor.client.vue'
import Modal from '../../../../global/Modal.vue'
import clonedeep from 'clone-deep'
import { default as _has } from 'lodash/has'

export default {
  name: 'FormBlockLogicEditor',
  components: { Modal, ConditionEditor },
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

  data () {
    return {
      resetKey: 0,
      logic: this.field.logic || {
        conditions: null,
        actions: []
      },
      showCopyFormModal: false,
      copyFrom: null
    }
  },

  computed: {
    copyFromOptions () {
      return this.form.properties.filter((field) => {
        return field.id !== this.field.id && _has(field, 'logic') && field.logic !== null && field.logic !== {}
      }).map((field) => {
        return { name: field.name, value: field.id }
      })
    },
    actionOptions () {
      if (['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'].includes(this.field.type)) {
        if (this.field.hidden) {
          return [{ name: 'Show Block', value: 'show-block' }]
        } else {
          return [{ name: 'Hide Block', value: 'hide-block' }]
        }
      }

      if (this.field.hidden) {
        return [
          { name: 'Show Block', value: 'show-block' },
          { name: 'Require answer', value: 'require-answer' }
        ]
      } else if (this.field.disabled) {
        return [
          { name: 'Enable Block', value: 'enable-block' },
          (this.field.required
            ? { name: 'Make it optional', value: 'make-it-optional' }
            : {
                name: 'Require answer',
                value: 'require-answer'
              })
        ]
      } else {
        return [
          { name: 'Hide Block', value: 'hide-block' },
          { name: 'Disable Block', value: 'disable-block' },
          (this.field.required
            ? { name: 'Make it optional', value: 'make-it-optional' }
            : {
                name: 'Require answer',
                value: 'require-answer'
              })
        ]
      }
    }
  },

  watch: {
    logic: {
      handler () {
        this.field.logic = this.logic
      },
      deep: true
    },
    'field.id': {
      handler (field, oldField) {
        // On field change, reset logic
        this.logic = this.field.logic || {
          conditions: null,
          actions: []
        }
      }
    },
    'field.required': 'cleanConditions',
    'field.disabled': 'cleanConditions',
    'field.hidden': 'cleanConditions'
  },

  mounted () {
    if (!_has(this.field, 'logic')) {
      this.field.logic = this.logic
    }
  },

  methods: {
    clearAll () {
      this.logic.conditions = null
      this.logic.actions = []
      this.refreshActions()
    },
    onActionInput () {
      if (this.logic.actions.length >= 2) {
        if (this.logic.actions[1] === 'require-answer' && this.logic.actions[0] === 'hide-block') {
          this.logic.actions = ['require-answer']
        } else if (this.logic.actions[1] === 'hide-block' && this.logic.actions[0] === 'require-answer') {
          this.logic.actions = ['hide-block']
        }
        this.refreshActions()
      }
    },
    cleanConditions () {
      const availableActions = this.actionOptions.map(function (op) { return op.value })
      this.logic.actions = availableActions.filter(value => this.logic.actions.includes(value))
      this.refreshActions()
    },
    refreshActions () {
      this.resetKey++
    },
    copyLogic () {
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
    }
  }
}
</script>
