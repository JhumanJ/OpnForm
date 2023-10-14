<template>
  <collapse class="py-5 w-full" :default-value="false">
    <template #title>
      <div class="flex">
        <h3 class="font-semibold block text-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-gray-500 mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg> Show advanced options
        </h3>
      </div>
    </template>
    <toggle-switch-input :value="value.hide_title" name="hide_title" class="mt-4"
        label="Hide Form Title"
        :disabled="form.hide_title===true"
        @input="onChangeHideTitle"
        :help="hideTitleHelp"
    />
    <toggle-switch-input :value="value.auto_submit" name="auto_submit" class="mt-4"
        label="Auto Submit Form"
        help="Form will auto submit immediate after open URL"
        @input="onChangeAutoSubmit"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../common/Collapse.vue'
export default {
  name: 'AdvancedFormUrlSettings',
  components: { Collapse },
  props: {
    form: {
      type: Object,
      required: true
    },
    value: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
    }
  },

  computed: {
    hideTitleHelp () {
      return this.form.hide_title ? 'This option is disabled because the form title is already hidden' : null
    }
  },

  watch: {},

  mounted () {},

  methods: {
    onChangeHideTitle (val) {
      this.value.hide_title = val
    },
    onChangeAutoSubmit (val) {
      this.value.auto_submit = val
    }
  }
}
</script>
