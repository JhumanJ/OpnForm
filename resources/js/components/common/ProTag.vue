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
      <h4 v-if="user.is_subscribed && !user.has_enterprise_subscription" class="text-center mt-5">
        We're happy to have you as a Pro customer. If you're having any issue with OpnForm, or if you have a
        feature request, please <a href="mailto:contact@opnform.com">contact us</a>.
        <br><br>
        If you need to collaborate, or to work with multiple workspaces, or just larger file uploads, you can
        also upgrade our subscription to get an Enterprise subscription.
      </h4>
      <h4 v-if="user.is_subscribed && user.has_enterprise_subscription" class="text-center mt-5">
        We're happy to have you as an Enterprise customer. If you're having any issue with OpnForm, or if you have a
        feature request, please <a href="mailto:contact@opnform.com">contact us</a>.
      </h4>
      <p v-if="!user.is_subscribed" class="mt-4">
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

      <p class="my-4 text-center">
        Feel free to <a href="mailto:contact@opnform.com">contact us</a> if you have any feature request.
      </p>
      <div class="mb-4 text-center">
        <v-button color="gray" shade="light" @click="showPremiumModal=false">
          Close
        </v-button>
      </div>
    </modal>
  </div>
</template>

<script>
import Modal from '../Modal'
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  name: 'ProTag',
  components: { Modal },
  props: {},

  data () {
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
      return false; //!this.user.is_subscribed && !(this.currentWorkSpace.is_pro || this.currentWorkSpace.is_enterprise);
    },
  },

  mounted () {
  },

  methods: {}
}
</script>
