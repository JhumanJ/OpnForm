<template>
  <div
    id="app"
    class="bg-white dark:bg-notion-dark"
  >
    <transition
      enter-active-class="linear duration-200 overflow-hidden"
      enter-from-class="max-h-0"
      enter-to-class="max-h-screen"
      leave-active-class="linear duration-200 overflow-hidden"
      leave-from-class="max-h-screen"
      leave-to-class="max-h-0"
    >
      <div
        v-if="announcement && !isIframe"
        class="bg-nt-blue text-white text-center p-3 relative"
      >
        <a
          class="text-white font-semibold"
          href=""
          target="_blank"
        >🚨 OpnForm beta is over 🚨</a>
        <div
          role="button"
          class="text-white absolute right-0 top-0 p-3 cursor-pointer"
          @click="announcement = false"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>
    </transition>

    <NuxtLoadingIndicator color="#2563eb" />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <ToolsStopImpersonation />

    <NotificationsWrapper />
    <ClientOnly>
      <feature-base />
      <SubscriptionModal />
      <QuickRegister />
    </ClientOnly>
  </div>
</template>

<script>
import { computed } from "vue"
import { useAppStore } from "~/stores/app"
import FeatureBase from "~/components/vendor/FeatureBase.vue"

export default {
  el: "#app",

  name: "OpnForm",

  components: { FeatureBase },

  setup() {
    const config = useRuntimeConfig()
    useOpnSeoMeta({
      title: "Beautiful forms & Surveys",
      description:
        "Create beautiful forms for free. Unlimited fields, unlimited submissions. It's free and it takes less than 1 minute to create your first form.",
      ogImage: "/img/social-preview.jpg",
      robots: () => {
        return config.public.env === "production" ? null : "noindex, nofollow"
      },
    })
    useHead({
      titleTemplate: (titleChunk) => {
        return titleChunk ? `${titleChunk} - OpnForm` : "OpnForm"
      },
      meta: [
        {
          name: 'apple-mobile-web-app-capable',
          content: 'yes'
        },
        {
          name: 'apple-mobile-web-app-status-bar-style',
          content: 'black-translucent'
        },
      ],
      link: [
        {
          rel: 'apple-touch-icon',
          type: 'image/png',
          href: '/favicon.ico'
        }
      ],
      htmlAttrs: () => ({
        dir: 'ltr'
      })
    })

    const appStore = useAppStore()

    return {
      layout: computed(() => appStore.layout),
      isIframe: useIsIframe(),
    }
  },

  data: () => ({
    announcement: false,
    alert: {
      type: null,
      autoClose: 0,
      message: "",
      confirmationProceed: null,
      confirmationCancel: null,
    },
    navbarHidden: false,
  }),

  computed: {
    isOnboardingPage() {
      return this.$route.name === "onboarding"
    },
  },

  mounted() {
    useCrisp().onCrispInit()
    useCrisp().showChat()
  },

  methods: {
    workspaceAdded() {
      this.$router.push({ name: "home" })
    },
    hideNavbar(hidden = true) {
      this.navbarHidden = hidden
    },
  },
}
</script>
