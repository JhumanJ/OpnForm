<template>
  <query-builder v-model="query" :rules="rules" :config="config" @input="onChange">
    <template #groupOperator="props">
      <div class="query-builder-group-slot__group-selection flex items-center px-5 border-b py-1 mb-1 flex">
        <p class="mr-2 font-semibold">
          Operator
        </p>
        <select-input
          wrapper-class="relative"
          :value="props.currentOperator"
          :options="props.operators"
          emit-key="identifier"
          option-key="identifier"
          name="operator-input"
          margin-bottom=""
          @input="props.updateCurrentOperator($event)"
        />
      </div>
    </template>
    <template #groupControl="props">
      <group-control-slot :group-ctrl="props" />
    </template>
    <template #rule="ruleCtrl">
      <component
        :is="ruleCtrl.ruleComponent"
        :value="ruleCtrl.ruleData"
        @input="ruleCtrl.updateRuleData"
      />
    </template>
  </query-builder>
</template>

<script>
import QueryBuilder from 'query-builder-vue'
import ColumnCondition from './ColumnCondition.vue'
import Vue from 'vue'
import GroupControlSlot from './GroupControlSlot.vue'

export default {

  components: {
    GroupControlSlot,
    QueryBuilder,
    ColumnCondition
  },

  props: {
    form: { type: Object, required: true },
    value: { required: false }
  },

  data () {
    return {
      query: this.value
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
            return Vue.extend(ColumnCondition).extend({
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
    value () {
      this.query = this.value
    }
  },

  methods: {
    onChange () {
      this.$emit('input', this.query)
    }
  }
}
</script>
