<template>
  <div>
    <toggle-switch-input name="include_submission_data" v-model="compVal.include_submission_data" class="mt-4"
                        label="Include submission data"
                        help="With form submission answers"
    />
    <toggle-switch-input name="link_open_form" v-model="compVal.link_open_form" class="mt-4"
                        label="'Open Form' Link"
                        help="Link to the form public page"
    />
    <toggle-switch-input name="link_edit_form" v-model="compVal.link_edit_form" class="mt-4"
                        label="'Edit Form' Link"
                        help="Link to the form admin page"
    />
    <toggle-switch-input name="views_submissions_count" v-model="compVal.views_submissions_count" class="mt-4"
                        label="Form Analytics" help="Form views and submissions count"
    />
    <toggle-switch-input name="link_edit_submission" v-model="compVal.link_edit_submission" class="mt-4"
                        label="Edit Submission Link"
    />
  </div>
</template>

<script>
export default {
  name: 'NotificationsMessageActions',
  components: { },
  props: {
    modelValue: { required: false }
  },
  data () {
    return {
      content: this.modelValue ?? {}
    }
  },

  computed: {
    compVal: {
      set (val) {
        this.content = val
        this.$emit('input', this.compVal)
      },
      get () {
        return this.content
      }
    }
  },

  watch: {
    modelValue (val) {
      this.content = val
    }
  },

  created () {
    if(this.compVal === undefined || this.compVal === null){
      this.compVal = {}
    }
    ['include_submission_data', 'link_open_form', 'link_edit_form', 'views_submissions_count', 'link_edit_submission'].forEach((keyname) => {
      if (this.compVal[keyname] === undefined) {
        this.compVal[keyname] = true
      }
    })
  },

  methods: { }
}
</script>
