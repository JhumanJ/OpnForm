<template>
  <collapse class="p-4 w-full border-b" v-model="isCollapseOpen">
    <template #title>
      <h3 class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2 transition-colors"
          :class="{'text-blue-600':isCollapseOpen, 'text-gray-500':!isCollapseOpen}"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
        </svg>
        Integrations
        <pro-tag/>
      </h3>
    </template>
    <text-input name="webhook_url" class="mt-4"
                :form="form" help="We will post form submissions to this endpoint."
                label="Webhook URL"
    />

<!--    <div>-->
<!--      <p>-->
<!--        <span class="text-uppercase font-semibold text-blue-500">NEW</span> - our Zapier integration is available for-->
<!--        beta testers!-->
<!--      </p>-->
<!--      <p class="w-full text-center mt-5">-->
<!--        <a :href="zapierUrl" target="_blank">-->
<!--          <v-button color="gray" shade="lighter">-->
<!--            <svg class="h-5 w-5 inline text-yellow-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg"-->
<!--                 viewBox="0 0 512 512"-->
<!--            >-->
<!--              <path-->
<!--                d="M318 256c0 19-4 36-10 52-16 7-34 10-52 10-19 0-36-3-52-9-7-17-10-34-10-53 0-18 3-36 10-52 16-6 33-10 52-10 18 0 36 4 52 10 6 16 10 34 10 52zm182-41H355l102-102c-8-11-17-22-26-32-10-9-21-18-32-26L297 157V12c-13-2-27-3-41-3s-28 1-41 3v145L113 55c-12 8-22 17-32 26-10 10-19 21-27 32l102 102H12s-3 27-3 41 1 28 3 41h144L54 399c16 23 36 43 59 59l102-102v144c13 2 27 3 41 3s28-1 41-3V356l102 102c11-8 22-17 32-27 9-10 18-20 26-32L355 297h145c2-13 3-27 3-41s-1-28-3-41z"-->
<!--              />-->
<!--            </svg>-->
<!--            Zapier Integration-->
<!--          </v-button>-->
<!--        </a>-->
<!--      </p>-->
<!--    </div>-->
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse.vue'
import ProTag from '../../../../common/ProTag.vue'

export default {
  components: {Collapse, ProTag},
  props: {},
  data() {
    return {
      isCollapseOpen: false
    }
  },

  computed: {
    form: {
      get() {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set(value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    zapierUrl: () => window.config.links.zapier_integration

  },

  watch: {},

  mounted() {},
}
</script>
