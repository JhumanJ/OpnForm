<template>
  <div
    v-if="user.active_license"
    class="border p-5 shadow-md rounded-md"
  >
    <div class="w-auto flex flex-col items-center">
      <img
        src="/img/appsumo/as-taco-white-bg.png"
        class="max-w-[60px]"
        alt="AppSumo"
      >

      <img
        src="/img/appsumo/as-Select-dark.png"
        class="max-w-[150px]"
        alt="AppSumo"
      >
    </div>
    <p class="mt-6">
      Your AppSumo
      <span class="font-semibold">lifetime deal tier {{ licenseTier }}</span>
      license is active. Here's a reminder of your plan details:
    </p>
    <ul class="list-disc pl-5 mt-4">
      <li>
        Number of Forms:
        <span class="font-semibold">{{ tierFeatures.form_quantity }}</span>
      </li>
      <li>
        Custom domains:
        <span class="font-semibold">{{ tierFeatures.domain_names }}</span>
      </li>
      <li>
        File Size Uploads:
        <span class="font-semibold">{{ tierFeatures.file_upload_size }}</span>
      </li>
      <li>
        Users limit:
        <span class="font-semibold">{{ tierFeatures.users }}</span>
      </li>
    </ul>
    <div class="w-max">
      <v-button
        color="outline-gray"
        shade="lighter"
        class="mt-4 block"
        href="https://appsumo.com/account/products/"
        target="_blank"
      >
        Manage in AppSumo
      </v-button>
    </div>
  </div>
</template>

<script>
import { computed } from "vue"
import VButton from "~/components/global/VButton.vue"

export default {
  name: "AppSumoBilling",
  components: { VButton },

  setup() {
    const authStore = useAuthStore()
    return {
      user: computed(() => authStore.user),
    }
  },

  data() {
    return {}
  },

  computed: {
    licenseTier() {
      return this.user?.active_license?.meta?.tier
    },
    tierFeatures() {
      if (!this.licenseTier) return {}
      return {
        1: {
          form_quantity: "Unlimited",
          file_upload_size: "25mb",
          domain_names: "5",
          users: 1
        },
        2: {
          form_quantity: "Unlimited",
          file_upload_size: "50mb",
          domain_names: "25",
          users: 5
        },
        3: {
          form_quantity: "Unlimited",
          file_upload_size: "75mb",
          domain_names: "Unlimited",
          users: 20
        },
      }[this.licenseTier]
    },
  },

  watch: {},

  mounted() {},

  created() {},

  unmounted() {},

  methods: {},
}
</script>
