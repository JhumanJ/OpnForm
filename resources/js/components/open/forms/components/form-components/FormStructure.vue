<template>
  <collapse class="p-5 w-full border-b" :default-value="true">
    <template #title>
      <div class="flex">
        <h3 id="v-step-1" class="font-semibold block text-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-gray-500 mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg> Form Structure
        </h3>
      </div>
    </template>
    <form-fields-editor class="mt-5" />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse'
import FormFieldsEditor from '../FormFieldsEditor'

export default {
  components: { Collapse, FormFieldsEditor },
  props: {
  },
  data () {
    return {
    }
  },

  computed: {
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    }
  },

  watch: {},

  mounted () {
  },

  methods: {
  }
}
</script>
