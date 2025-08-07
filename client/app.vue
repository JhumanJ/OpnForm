<template>
  <AppProvider>
    <div
      id="app"
      class="bg-white dark:bg-notion-dark"
    >
      <NuxtLoadingIndicator color="#2563eb" />
      <NuxtLayout>
        <NuxtPage />
      </NuxtLayout>

      <ClientOnly>
        <div
          class="fixed z-[9999] left-0 bottom-0 p-4" id="admin-actions"
        >
          <UButtonGroup size="sm">
            <ToolsStopImpersonation />
          </UButtonGroup>
        </div>
      </ClientOnly>

      <ClientOnly>
        <FeatureBase />
        <SubscriptionModal />
        <QuickRegister />
      </ClientOnly>
    </div>
  </AppProvider>
</template>

<script setup>
import FeatureBase from "~/components/vendor/FeatureBase.vue"

const config = useRuntimeConfig()

// SEO and head configuration
useOpnSeoMeta({
  title: "Free Form Builder with Unlimited Submissions",
  description:
    "Build beautiful, powerful forms for free with OpnForm. Unlimited submissions, rich features, and seamless integrations — fully open-source and easy to use.",
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
</script>
