<template>
  <div v-if="logic" :key="resetKey" class="-mx-4 sm:-mx-6 p-5 border-b">
    <h3 class="font-semibold block text-lg">
      Logic
      <pro-tag />
    </h3>
    <p class="text-gray-400 mb-5">
      Add some logic to this block. Start by adding some conditions, and then add some actions.
    </p>
    <div class="relative">
      <v-button size="small" @click="showCopyFormModal=true">
        Copy from...
      </v-button>
      <v-button color="red" shade="light" size="small" class="ml-1" @click="clearAll">
        Clear All
      </v-button>
    </div>

    <h5 class="font-semibold mt-4">
      1. Conditions
    </h5>
    <condition-editor ref="filter-editor" v-model="logic.conditions" class="mt-4 border-t border" :form="form" />

    <h5 class="font-semibold mt-4">
      2. Actions
    </h5>
    <select-input :key="resetKey" v-model="logic.actions" name="actions"
                  :multiple="true" class="mt-4" placeholder="Actions..."
                  help="Action(s) triggerred when above conditions are true"
                  :options="actionOptions"
                  @input="onActionInput"
    />

    <modal :show="showCopyFormModal" @close="showCopyFormModal">
      <h3 class="font-semibold block text-lg">
        Copy logic from another field
      </h3>
      <p class="text-gray-400 mb-5">
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
    </modal>
  </div>
</template>

<script>
import ProTag from '../../../../common/ProTag'
import ConditionEditor from './ConditionEditor'
import Modal from '../../../../Modal'
import SelectInput from '../../../../forms/SelectInput'
import clonedeep from 'clone-deep'

export default {
  name: 'FormBlockLogicEditor',
  components: { SelectInput, Modal, ProTag, ConditionEditor },
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
        return field.id !== this.field.id
      }).map((field) => {
        return { name: field.name, value: field.id }
      })
    },
    actionOptions () {
      if (['nf-text', 'nf-page-break', 'nf-divider', 'nf-image'].includes(this.field.type)) {
        return [{ name: 'Hide Block', value: 'hide-block' }]
      }

      if (this.field.hidden) {
        return [
          { name: 'Show Block', value: 'show-block' },
          { name: 'Require answer', value: 'require-answer' }
        ]
      } else {
        return [
          { name: 'Hide Block', value: 'hide-block' },
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
        this.$set(this.field, 'logic', this.logic)
      },
      deep: true
    },
    'field.required': {
      handler () {
        this.cleanConditions()
      },
      deep: true
    }
  },

  mounted () {
    if (!this.field.hasOwnProperty('logic')) {
      this.$set(this.field, 'logic', this.logic)
    }
  },

  methods: {
    clearAll () {
      this.$set(this.logic, 'conditions', null)
      this.$set(this.logic, 'actions', [])
      this.refreshActions()
    },
    onActionInput () {
      if (this.logic.actions.length >= 2) {
        if (this.logic.actions[1] === 'require-answer' && this.logic.actions[0] === 'hide-block') {
          this.$set(this.logic, 'actions', ['require-answer'])
        } else if (this.logic.actions[1] === 'hide-block' && this.logic.actions[0] === 'require-answer') {
          this.$set(this.logic, 'actions', ['hide-block'])
        }
        this.refreshActions()
      }
    },
    cleanConditions () {
      if (this.required && this.logic.actions.includes('require-answer')) {
        this.$set(this.logic, 'actions', this.logic.actions.filter((action) => action !== 'require-answer'))
      } else if (!this.required && this.logic.actions.includes('make-it-optional')) {
        this.$set(this.logic, 'actions', this.logic.actions.filter((action) => action !== 'make-it-optional'))
      }
      this.resetKey++
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
          this.$set(this, 'logic', clonedeep(property.logic))
          this.cleanConditions()
        }
      }
      this.showCopyFormModal = false
    }
  }
}
</script>
