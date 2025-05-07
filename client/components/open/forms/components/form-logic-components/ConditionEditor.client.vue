<template>
  <ErrorBoundary
    ref="error_boundary"
    @on-error="handleError"
  >
    <query-builder
      v-model="query"
      :rules="rules"
      :config="config"
      v-bind="$attrs"
      @update:model-value="onChange"
    >
      <template #groupOperator="props">
        <div
          class="query-builder-group-slot__group-selection flex items-center px-5 border-b py-1"
        >
          <p class="mr-2 mt-1 font-semibold">
            Operator
          </p>
          <select-input
            wrapper-class="relative mb-0" 
            :model-value="props.currentOperator"
            :options="props.operators"
            emit-key="identifier"
            option-key="identifier"
            name="operator-input"
            :help="null"
            margin-bottom=""
            @update:model-value="props.updateCurrentOperator($event)"
          />
        </div>
      </template>
      <template #groupControl="props">
        <group-control-slot :group-ctrl="props" />
      </template>
      <template #rule="ruleCtrl">
        <component
          :is="ruleCtrl.ruleComponent"
          :model-value="ruleCtrl.ruleData"
          @update:model-value="ruleCtrl.updateRuleData"
        />
      </template>
    </query-builder>
  </ErrorBoundary> 
</template>

<script>
 
import { defineComponent } from "vue"
import QueryBuilder from "query-builder-vue-3"
import ColumnCondition from "./ColumnCondition.vue"
import GroupControlSlot from "./GroupControlSlot.vue"

export default {
  components: {
    GroupControlSlot,
    QueryBuilder,
    ColumnCondition,
  },

  props: {
    form: { type: Object, required: true },
    modelValue: { type: Object, required: false },
    customValidation: { type: Boolean, default: false },
  },
  emits:  ['update:modelValue'],

  data() {
    return {
      query: this.modelValue,
    }
  },

  computed: {
    rules() {
      return this.form.properties
        .filter((property) => {
          return !property.type.startsWith("nf-")
        })
        .map((property) => {
          const workspaceId = this.form.workspace_id
          const formSlug = this.form.slug
          const customValidation = this.customValidation
          return {
            identifier: property.id,
            name: property.name,
            component: (function () {
              return defineComponent({
                extends: ColumnCondition,
                props: {
                  customValidation: {
                    type: Boolean,
                    default: customValidation
                  }
                },
                computed: {
                  property() {
                    return property
                  },
                  viewContext() {
                    return {
                      form_slug: formSlug,
                      workspace_id: workspaceId,
                    }
                  },
                },
              })
            })(),
          }
        })
    },

    config() {
      return {
        operators: [
          {
            name: "And",
            identifier: "and",
          },
          {
            name: "Or",
            identifier: "or",
          },
        ],
        rules: this.rules,
        colors: [
          "#ef4444",
          "#22c55e",
          "#f97316",
          "#0ea5e9",
          "#8b5cf6",
          "#ec4899",
        ],
      }
    },
  },

  watch: {
    modelValue() {
      this.query = this.modelValue
    },
  },

  methods: {
    onChange() {
      this.$emit("update:modelValue", this.query)
    },
    // If there was some changes to the structure, causing an issue, we clear the condition
    handleError (error) {
      this.$nextTick(() => {
        console.error('Error with condition - clearing previous value', error)
        this.query = null
        this.onChange()
        this.$refs['error_boundary'].clearError()
      })
    }
  }
}
</script>
<style src="query-builder-vue-3/dist/style.css" />
