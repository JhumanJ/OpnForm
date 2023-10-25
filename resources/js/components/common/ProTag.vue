<template>
  <div class="inline" v-if="shouldDisplayProTag">
    <div class="bg-nt-blue text-white px-2 text-xs uppercase inline rounded-full font-semibold cursor-pointer"
      @click.prevent="showPremiumModal=true">
      PRO
    </div>
    <modal :show="showPremiumModal" @close="showPremiumModal=false">
      <h2 class="text-nt-blue">
        e-Form PRO
      </h2>
      <h4 v-if="user && user.is_subscribed" class="text-center mt-5">
        We're happy to have you as a Pro customer. If you're having any issue with OpnForm, or if you have a
        feature request, please <a href="mailto:contact@opnform.com">contact us</a>.
      </h4>
      <div v-if="!user || !user.is_subscribed" class="mt-4">
        <p>
          Tất cả các tính năng có<span
            class="bg-nt-blue text-white px-2 text-xs uppercase inline rounded-full font-semibold mx-1">
            PRO
          </span> có sẵn trong gói Pro của e-Form. <b>Bạn có thể thử và thử tất cả các tính năng Pro
            ở trong
            trình chỉnh sửa biểu mẫu, nhưng bạn không thể sử dụng chúng trong biểu mẫu thực của mình</b>. Bạn có thể đăng
          ký ngay bây giờ để có được quyền truy cập không giới hạn
          ĐẾN
          tất cả các tính năng PRO của chúng tôi!
        </p>
      </div>

      <div class="my-4 text-center">
        <v-button color="white" @click="showPremiumModal=false">
          Đóng
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
