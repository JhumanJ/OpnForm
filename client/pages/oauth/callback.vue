<template>
  <div class="flex flex-grow mt-6 mb-10">
    <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
      <div
        v-if="loading"
        class="m-10"
      >
        <h3 class="my-6 text-center">
          Please wait...
        </h3>
        <Loader class="h-6 w-6 mx-auto m-10" />
      </div>
      <div
        v-else
        class="m-6 flex flex-col items-center space-y-4"
      >
        <p class="text-center">
          Unable to sign it at the moment.
        </p>
        <v-button
          :to="{ name: 'login' }"
        >
          Back to login
        </v-button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useNuxtApp } from "nuxt/app"

const { $utm } = useNuxtApp()
const router = useRouter()
const route = useRoute()
const loading = ref(true)

definePageMeta({
    alias: '/oauth/:provider/callback'
})

const handleCallback = async () => {
  const auth = useAuth()
  const provider = route.params.provider
  
  try {
    const { isNewUser } = await auth.handleSocialCallback(
      provider,
      route.query.code,
      $utm.value
    )

    if (!isNewUser) {
      // Handle existing user login
      if (window.opener) {
        window.opener.document.dispatchEvent(new CustomEvent('quick-login-complete'))
      }
      window.close()
    } else {
      // Handle new user registration
      router.push({ name: "forms-create" })
      useAlert().success("Success! You're now registered with your Google account! Welcome to OpnForm.")
    }
  } catch (error) {
    useAlert().error(error.response._data.message)
    loading.value = false
  }
}

onMounted(() => {
    handleCallback()
})

</script>