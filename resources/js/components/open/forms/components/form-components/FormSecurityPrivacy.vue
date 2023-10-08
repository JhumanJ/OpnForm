<template>
  <collapse class="p-4 w-full border-b" v-model="isCollapseOpen">
    <template #title>
      <h3 id="v-step-2" class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2 transition-colors"
          :class="{'text-blue-600':isCollapseOpen, 'text-gray-500':!isCollapseOpen}"
          fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg> Security & Privacy
      </h3>
    </template>
    <toggle-switch-input name="can_be_indexed" :form="form" class="mt-4"
                    label="Indexable by Google"
                    help="If enabled, your form can appear in the search results of Google"
    />
    <toggle-switch-input name="use_captcha" :form="form" class="mt-4"
                    label="Protect your form with a Captcha"
                    help="If enabled we will make sure respondant is a human"
    />
    <text-input name="password" :form="form" class="mt-4"
                    label="Form Password" help="Leave empty to disable password"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse.vue'
import ProTag from '../../../../common/ProTag.vue'

export default {
  components: { Collapse, ProTag },
  props: {
  },
  data () {
    return {
      isCollapseOpen: false
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
}
</script>
