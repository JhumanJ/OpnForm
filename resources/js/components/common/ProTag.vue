<template>
  <div class="inline" v-if="shouldDisplayProTag">
    <div class="bg-nt-blue text-white px-2 text-xs uppercase inline rounded-full font-semibold cursor-pointer"
         @click.prevent="showPremiumModal=true"
    >
      PRO
    </div>
    <modal :show="showPremiumModal" @close="showPremiumModal=false">
      <h2 class="text-nt-blue">
        OpnForm PRO
      </h2>
      <h4 v-if="user && user.is_subscribed" class="text-center mt-5">
        We're happy to have you as a Pro customer. If you're having any issue with OpnForm, or if you have a
        feature request, please <a href="mailto:contact@opnform.com">contact us</a>.
      </h4>
      <div v-if="!user || !user.is_subscribed" class="mt-4">
        <p>
          All the features with a<span
          class="bg-nt-blue text-white px-2 text-xs uppercase inline rounded-full font-semibold mx-1"
        >
          PRO
        </span> tag are available in the Pro plan of OpnForm. <b>You can play around and try all Pro features
          within
          the form editor, but you can't use them in your real forms</b>. You can subscribe now to gain unlimited access
          to
          all our pro features!
        </p>
      </div>

      <div class="my-4 text-center">
        <v-button color="white" @click="showPremiumModal=false">
          Close
        </v-button>
      </div>
    </modal>
  </div>
</template>

<script>
import Modal from '../Modal.vue'
import {mapGetters} from 'vuex'
import PricingTable from "../pages/pricing/PricingTable.vue";

export default {
  name: 'ProTag',
  components: {PricingTable, Modal},
  props: {},

  data() {
    return {
      showPremiumModal: false,
      checkoutLoading: false
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      currentWorkSpace: 'open/workspaces/getCurrent',
    }),
    shouldDisplayProTag() {
      if (!window.config.paid_plans_enabled) return false
      if (!this.user || !this.currentWorkSpace) return true
      return !(this.currentWorkSpace.is_pro)
    },
  },

  mounted() {
  },

  methods: {
    openChat() {
      window.$crisp.push(['do', 'chat:show'])
      window.$crisp.push(['do', 'chat:open'])
    },
  }
}
</script>
