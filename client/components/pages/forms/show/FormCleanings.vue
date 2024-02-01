<template>
  <v-transition>
    <div v-if="hasCleanings && !hideWarning" class="border border-gray-300 dark:border-gray-600 rounded-md bg-white p-2"
         :class="{'hover:bg-yellow-50 dark:hover:bg-yellow-900':!collapseOpened}"
    >
      <collapse v-model="collapseOpened">
        <template #title>
          <p class="text-yellow-500 dark:text-yellow-400 font-semibold text-sm p-1 pr-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-6 h-6 inline"
            >
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
              />
            </svg>
            Some features that are included in our {{ form.is_pro ? 'Enterprise' : 'Pro' }} plan are disabled when
            publicly sharing this form<span v-if="specifyFormOwner"> (only owners of this form can see this)</span>.
          </p>
        </template>

        <div class="border-t mt-1 p-4 pb-2 -mx-2">
          <p class="text-gray-500 text-sm" v-html="cleaningContent" />
          <p class="text-gray-500 text-sm mb-4 font-semibold">
            <NuxtLink :to="{name:'pricing'}">
              {{ form.is_pro ? 'Upgrade your OpnForms plan today' : 'Start your free OpnForms trial' }}
            </NuxtLink>
            to unlock all of our features and build powerful forms.
          </p>
          <div class="flex flex-wrap items-end w-full">
            <div class="flex-grow flex pr-2">
              <v-button v-track.upgrade_from_form_cleanings_click size="small" class="inline-block" :to="{name:'pricing'}">
                {{ form.is_pro ? 'Upgrade plan' : 'Start free trial' }}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-4 h-4 inline -mt-[2px]"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
              </v-button>
              <v-button color="white" size="small" class="ml-2" @click.prevent="openCrisp">
                Contact us
              </v-button>
            </div>
            <v-button v-if="hideable" color="white" size="small" class="mt-2" @click.prevent="hideWarning=true">
              Hide warning
            </v-button>
          </div>
        </div>
      </collapse>
    </div>
  </v-transition>
</template>
<script>

import Collapse from '~/components/global/Collapse.vue'
import VButton from '~/components/global/VButton.vue'
import VTransition from '~/components/global/transitions/VTransition.vue'

export default {
  name: 'FormCleanings',
  components: { VTransition, VButton, Collapse },
  props: {
    form: { type: Object, required: true },
    specifyFormOwner: { type: Boolean, default: false },
    hideable: { type: Boolean, default: false }
  },
  data () {
    return {
      collapseOpened: false,
      hideWarning: false
    }
  },
  computed: {
    hasCleanings () {
      return this.form.cleanings && Object.keys(this.form.cleanings).length > 0
    },
    cleanings () {
      return this.form.cleanings
    },
    cleaningContent () {
      let message = ''
      Object.keys(this.cleanings).forEach((key) => {
        let fieldName = key.charAt(0).toUpperCase() + key.slice(1)
        if (fieldName !== 'Form') {
          fieldName = '"' + fieldName + '" field'
        }
        let fieldInfo = '<span class="font-semibold">' + fieldName + '</span><br/><ul class=\'list-disc list-inside\'>'
        this.cleanings[key].forEach((msg) => {
          fieldInfo = fieldInfo + '<li>' + msg + '</li>'
        })
        if (fieldInfo) {
          message = message + fieldInfo + '<ul/><br/>'
        }
      })
      return message
    }
  },
  watch: {},
  mounted () {
  },
  methods: {
    openCrisp () {
      useCrisp().openAndShowChat()
    }
  }
}
</script>
