<template>
  <div>
    <NuxtLayout>
      <div class="flex mt-6">
        <div class="w-full md:w-2/3 md:mx-auto md:max-w-md">
          <img
            alt="Nice plant as we have nothing else to show!"
            src="/img/icons/plant.png"
            class="w-56 mb-5"
          >

          <h1 class="mb-6 font-semibold text-3xl text-gray-900">
            Whoops, something went wrong ({{ error.statusCode || '404' }})
          </h1>

          <div class="links">
            <NuxtLink
              :to="{ name: 'index' }"
              class="hover:underline text-gray-700"
            >
              Go Home
            </NuxtLink>
          </div>
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup>
import { captureException } from '@sentry/core'

useOpnSeoMeta({
  title: "404 - Page not found",
})

const props = defineProps({
  error: { type: Object, default: null }
})
const authStore = useAuthStore()


if (props.error?.statusCode === 500) {
  // Track in Sentry 500 errors
  const exception = new Error(props.error?.message ?? props.error?.statusMessage)
  exception.code = props.error?.statusCode
  exception.stack = props.error?.stack
  captureException(exception, {
    message: props.error?.message ?? props.error?.statusMessage,
    type: '500_error',
    user_id: authStore.user?.id
  })
}
</script>
