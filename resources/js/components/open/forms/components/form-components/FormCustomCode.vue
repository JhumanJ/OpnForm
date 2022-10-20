<template>
  <collapse class="py-3 w-full border-b" :default-value="false">
    <template #title>
      <h3 class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-nt-blue mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
        </svg>
        Custom Code
        <pro-tag />
      </h3>
    </template>
    <p class="mt-4">
      The code will be injected in the <b>head</b> section of your form page. <a href="#" class="text-gray-500"
                                                                                 @click.prevent="$getCrisp().push(['do', 'helpdesk:article:open', ['en', 'how-to-inject-custom-code-in-my-form-1amadj3']])"
      >Click
        here to get an example CSS code.</a>
    </p>
    <code-input name="custom_code" class="mt-4"
                :form="form" help="Custom code cannot be previewed in our editor. Please test your code using
                     your actual form page (save changes beforehand)."
                label="Custom Code"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse'
import ProTag from '../../../../common/ProTag'
import CodeInput from '../../../../forms/CodeInput'

export default {
  components: { Collapse, ProTag, CodeInput },
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
