<template>
  <div v-if="changeTypeOptions.length > 0">
    <dropdown dusk="nav-dropdown">
      <template #trigger="{toggle}">
        <v-button class="relative" color="outline-blue" @click="toggle">
          Change Field Type
        </v-button>
      </template>
        
      <a href="#" v-for="(op, index) in changeTypeOptions" :key="index"
            class="block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
            @click.prevent="changeType(op.value)"
        >
        {{ op.name }}
      </a>
    </dropdown>
  </div>
</template>

<script>
import Dropdown from '../../../../common/Dropdown'

export default {
  name: 'ChangeFieldType',
  components: { Dropdown },
  props: {
    field: {
      type: Object,
      required: true
    }
  },
  data () {
    return {

    }
  },

  computed: {
    changeTypeOptions () {
      var newTypes = []
      if (['text', 'email', 'phone', 'number'].includes(this.field.type)){
        newTypes = [
          {'name':'Text Input', 'value':'text'}, 
          {'name':'Email Input', 'value':'email'}, 
          {'name':'Phone Input', 'value':'phone'}, 
          {'name':'Number Input', 'value':'number'}
        ]
      }
      if (['select', 'multi_select'].includes(this.field.type)){
        newTypes = [
          {'name':'Select Input', 'value':'select'},
          {'name':'Multi-Select Input', 'value':'multi_select'}
        ]
      }
      return newTypes.filter((item) => { return item.value !==  this.field.type }).map((item) => {
        return {
          name: item.name,
          value: item.value
        }
      })
    }
  },

  watch: {},

  mounted () {},

  methods: {
    changeType (newType) {
      if(newType){
        this.$emit('changeType', newType)
      }
    }
  }
}
</script>
