<template>
  <query-builder v-model="query" :rules="rules" :config="config" @update:model-value="onChange">
    <template #groupOperator="props">
      <div class="query-builder-group-slot__group-selection flex items-center px-5 border-b py-1 mb-1 flex">
        <p class="mr-2 font-semibold">
          Operator
        </p>
        <select-input
          wrapper-class="relative"
          :model-value="props.currentOperator"
          :options="props.operators"
          emit-key="identifier"
          option-key="identifier"
          name="operator-input"
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
</template>

<style src="query-builder-vue-3/dist/style.css" />
<script>
import { defineComponent } from 'vue'
import QueryBuilder from 'query-builder-vue-3'
import ColumnCondition from './ColumnCondition.vue'
import GroupControlSlot from './GroupControlSlot.vue'

export default {

  components: {
    GroupControlSlot,
    QueryBuilder,
    ColumnCondition
  },

  props: {
    form: { type: Object, required: true },
    modelValue: { required: false }
  },

  data () {
    return {
      query: this.modelValue
    }
  },

  computed: {
    rules () {
      return this.form.properties.filter((property) => {
        return !property.type.startsWith('nf-')
      }).map((property) => {
        const workspaceId = this.form.workspace_id
        const formSlug = this.form.slug
        return {
          identifier: property.id,
          name: property.name,
          component: (function () {
            return defineComponent({
              extends: ColumnCondition,
              computed: {
                property () {
                  return property
                },
                viewContext () {
                  return {
                    form_slug: formSlug,
                    workspace_id: workspaceId
                  }
                }
              }
            })
          })()
        }
      })
    },

    config () {
      return {
        operators: [
          {
            name: 'And',
            identifier: 'and'
          },
          {
            name: 'Or',
            identifier: 'or'
          }
        ],
        rules: this.rules,
        colors: ['#ef4444', '#22c55e', '#f97316', '#0ea5e9', '#8b5cf6', '#ec4899']
      }
    }

  },

  watch: {
    modelValue () {
      this.query = this.modelValue
    }
  },

  methods: {
    onChange () {
      this.$emit('update:modelValue', this.query)
    }
  }
}
</script>
