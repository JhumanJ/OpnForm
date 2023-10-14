<template>
  <dropdown dusk="nav-dropdown" v-if="changeTypeOptions.length > 0">
    <template #trigger="{toggle}">
      <v-button class="relative" :class="btnClasses" size="small" color="light-gray" @click="toggle">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-blue-600 inline mr-1 -mt-1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
        </svg>
        <span class="whitespace-nowrap">Change Type</span>
      </v-button>
    </template>

    <a href="#" v-for="(op, index) in changeTypeOptions" :key="index"
       class="block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
       @click.prevent="changeType(op.value)"
    >
      {{ op.name }}
    </a>
  </dropdown>
</template>

<script>
import Dropdown from '../../../../common/Dropdown.vue'

export default {
  name: 'ChangeFieldType',
  components: {Dropdown},
  props: {
    field: {
      type: Object,
      required: true
    },
    btnClasses: {
      type: String,
      required: true
    }
  },
  data() {
    return {}
  },

  computed: {
    changeTypeOptions() {
      var newTypes = []
      if (['text', 'email', 'phone', 'number'].includes(this.field.type)) {
        newTypes = [
          {'name': 'Text Input', 'value': 'text'},
          {'name': 'Email Input', 'value': 'email'},
          {'name': 'Phone Input', 'value': 'phone'},
          {'name': 'Number Input', 'value': 'number'}
        ]
      }
      if (['select', 'multi_select'].includes(this.field.type)) {
        newTypes = [
          {'name': 'Select Input', 'value': 'select'},
          {'name': 'Multi-Select Input', 'value': 'multi_select'}
        ]
      }
      return newTypes.filter((item) => {
        return item.value !== this.field.type
      }).map((item) => {
        return {
          name: item.name,
          value: item.value
        }
      })
    }
  },

  watch: {},

  mounted() {
  },

  methods: {
    changeType(newType) {
      if (newType) {
        this.$emit('changeType', newType)
      }
    }
  }
}
</script>
