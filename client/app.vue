<template>
  <UApp :toaster="toasterConfig">
    <div
      id="app"
      class="bg-white dark:bg-notion-dark"
    >
      <NuxtLoadingIndicator color="#2563eb" />
      <NuxtLayout>
        <NuxtPage />
      </NuxtLayout>
      <ToolsStopImpersonation />

      <ClientOnly>
        <feature-base />
        <SubscriptionModal />
        <QuickRegister />
      </ClientOnly>
    </div>
  </UApp>
</template>

<script setup>
import { computed, onMounted } from "vue"
import { useAppStore } from "~/stores/app"
import FeatureBase from "~/components/vendor/FeatureBase.vue"

const config = useRuntimeConfig()
const appStore = useAppStore()

// SEO and head configuration
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

// Get Crisp chat state from the store
const crispChatOpened = computed(() => appStore.crisp.chatOpened)
const crispHidden = computed(() => appStore.crisp.hidden)

// Configure toaster positioning based on Crisp chat state
const toasterConfig = computed(() => {
  const baseConfig = {
    position: 'bottom-right',
    duration: 5000,
    expand: true,
  }

  // Dynamically adjust the UI class based on Crisp chat state
  if (crispHidden.value) {
    // Crisp is hidden: normal bottom-right position
    return {
      ...baseConfig,
      ui: {
        viewport: 'end-4 bottom-4',
      },
    }
  }

  if (crispChatOpened.value) {
    // Crisp chat is opened: keep default bottom-right (chat overlay already shifted)
    return {
      ...baseConfig,
      ui: {
        viewport: 'end-4 bottom-4',
      },
    }
  }

  // Crisp chat is closed but visible: move toasts above the chat button
  return {
    ...baseConfig,
    ui: {
      viewport: 'end-4 bottom-24',
    },
  }
})

// Lifecycle
onMounted(() => {
  useCrisp().onCrispInit()
  useCrisp().showChat()
})
</script>
