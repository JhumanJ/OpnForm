<template>
  <div v-if="user.active_license" class="border p-5 shadow-md rounded-md">
    <div class="w-auto flex flex-col items-center">
      <img :src="asset('img/appsumo/as-taco-white-bg.png')" class="max-w-[60px]" alt="AppSumo">
      <img :src="asset('img/appsumo/as-Select-dark.png')" class="max-w-[150px]" alt="AppSumo">
    </div>
    <p class="mt-6">
      Your AppSumo <span class="font-semibold">lifetime deal tier {{ licenseTier }}</span> license is active. Here's a reminder of your plan details:
    </p>
    <ul class="list-disc pl-5 mt-4">
      <li>Number of Forms: <span class="font-semibold">{{ tierFeatures.form_quantity }}</span></li>
      <li>Custom domains: <span class="font-semibold">{{ tierFeatures.domain_names }}</span></li>
      <li>File Size Uploads: <span class="font-semibold">{{ tierFeatures.file_upload_size }}</span></li>
    </ul>
    <div class="w-max">
      <v-button color="outline-gray" shade="lighter" class="mt-4 block" href="https://appsumo.com/account/products/" target="_blank">
        Mangage in AppSumo
      </v-button>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import VButton from '../../common/Button.vue'

export default {

  name: 'AppSumoBilling',
  components: { VButton },

  data () {
    return {
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    licenseTier () {
      return this.user?.active_license?.meta?.tier
    },
    tierFeatures () {
      if (!this.licenseTier) return {}
      return {
        1: {
          form_quantity: 'Unlimited',
          file_upload_size: '25mb',
          domain_names: '5'
        },
        2: {
          form_quantity: 'Unlimited',
          file_upload_size: '50mb',
          domain_names: '25'
        },
        3: {
          form_quantity: 'Unlimited',
          file_upload_size: '75mb',
          domain_names: 'Unlimited'
        }
      }[this.licenseTier]
    }
  },

  watch: {},

  mounted () {},

  created () {
  },
  destroyed () {
  },

  methods: {}
}
</script>
