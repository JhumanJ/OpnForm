<template>
  <div v-if="content" class="flex flex-wrap">
    <div class="w-full font-semibold text-gray-700 dark:text-gray-300 mb-2">
      {{ property.name }}
    </div>
    <SelectInput v-model="content.operator" class="w-full" :options="operators" :name="'operator_' + property.id"
      placeholder="Comparison operator" @update:model-value="operatorChanged()" />

    <template v-if="needsInput">
      <component v-bind="inputComponentData" :is="inputComponentData.component" v-model="content.value" class="w-full"
        :name="'value_' + property.id" placeholder="Filter Value" @update:model-value="emitInput()" />
    </template>
  </div>
</template>

<script>
import OpenFilters from '../../../../../data/open_filters.json'

export default {
  components: {},
  props: {
    modelValue: { required: true }
  },

  data() {
    return {
      content: { ...this.modelValue },
      available_filters: OpenFilters,
      hasInput: false,
      inputComponent: {
        text: 'TextInput',
        number: 'TextInput',
        rating: 'TextInput',
        scale: 'TextInput',
        slider: 'TextInput',
        select: 'SelectInput',
        multi_select: 'SelectInput',
        date: 'DateInput',
        files: 'FileInput',
        checkbox: 'CheckboxInput',
        url: 'TextInput',
        email: 'TextInput',
        phone_number: 'TextInput'
      }
    }
  },

  computed: {
    // Return type of input, and props for that input
    inputComponentData() {
      const componentData = {
        component: this.inputComponent[this.property.type],
        name: this.property.id,
        required: true
      }

      if (this.property.type === 'phone_number' && !this.property.use_simple_text_input) {
        componentData.component = 'PhoneInput'
      }

      if (['select', 'multi_select'].includes(this.property.type)) {
        componentData.multiple = false
        componentData.options = this.property[this.property.type].options.map(option => {
          return {
            name: option.name,
            value: option.name
          }
        })
      } else if (this.property.type === 'date') {
        // componentData.withTime = true
      } else if (this.property.type === 'checkbox') {
        componentData.label = this.property.name
      }

      return componentData
    },
    operators() {
      return Object.keys(this.available_filters[this.property.type].comparators).map(key => {
        return {
          value: key,
          name: this.optionFilterNames(key, this.property.type)
        }
      })
    },
    needsInput() {
      const operator = this.selectedOperator()
      if (!operator) {
        return false
      }
      const operatorFormat = operator.format
      if (!operatorFormat) return true

      if (operator.expected_type === 'boolean' && operatorFormat.type === 'enum' && operatorFormat.values.length === 1) {
        return false
      } else if (operator.expected_type === 'object' && operatorFormat.type === 'empty' && operatorFormat.values === '{}') {
        return false
      }

      return true
    },
  },

  watch: {
    modelValue() {
      this.refreshContent()
    },
    'content.operator': function (val) {
      if (val) {
        this.operatorChanged()
      }
    }
  },

  methods: {
    castContent(content) {
      if (['number', 'rating', 'scale', 'slider'].includes(this.property.type) && content.value) {
        content.value = Number(content.value)
      }

      const operator = this.selectedOperator()
      if (operator.expected_type === 'boolean') {
        content.value = Boolean(content.value)
      }

      return content
    },
    operatorChanged() {
      if (!this.content.operator) {
        return
      }

      const operator = this.selectedOperator()
      const operatorFormat = operator.format

      if (operator.expected_type === 'boolean' && operatorFormat.type === 'enum' && operatorFormat.values.length === 1) {
        this.content.value = operator.format.values[0]
      } else if (operator.expected_type === 'object' && operatorFormat.type === 'empty' && operatorFormat.values === '{}') {
        this.content.value = {}
      } else if (typeof this.content.value === 'boolean' || typeof this.content.value === 'object') {
        this.content.value = null
      }
      this.emitInput()
    },
    selectedOperator() {
      if (!this.content.operator) {
        return null
      }
      return this.available_filters[this.property.type].comparators[this.content.operator]
    },
    optionFilterNames(key, propertyType) {
      if (propertyType === 'checkbox') {
        return {
          equals: 'Is checked',
          does_not_equal: 'Is not checked'
        }[key]
      }
      return key.split('_').map(function (item) {
        return item.charAt(0).toUpperCase() + item.substring(1)
      }).join(' ')
    },
    emitInput() {
      this.$emit('update:modelValue', this.castContent(this.content))
    },
    refreshContent() {
      this.content = {
        operator: this.operators[0].value,
        ...this.modelValue,
        property_meta: {
          id: this.property.id,
          type: this.property.type
        }
      }
    }
  },

  mounted() {
    this.refreshContent()
  }
}
</script>
