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
          Unable to sign in at the moment.
        </p>
        <UButton
          :to="{ name: 'login' }"
          label="Back to login"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useNuxtApp } from "nuxt/app"
import { WindowMessageTypes } from "~/composables/useWindowMessage"
import { authApi } from "~/api"

const { $utm } = useNuxtApp()
const router = useRouter()
const route = useRoute()
const loading = ref(true)
const authFlow = useAuthFlow()


const handleCallback = async () => {
  const provider = route.params.provider
  
  try {
    let payloadData = {
      code: route.query.code,
      utm_data: $utm.value
    }
    // Get invite token from localStorage if it was stored during OAuth initiation
    const inviteToken = localStorage.getItem('oauth_invite_token')
    if (inviteToken) {
      payloadData.invite_token = inviteToken
      localStorage.removeItem('oauth_invite_token')
    }

    // Call the OAuth callback endpoint directly to get the raw response
    const response = await authApi.oauth.callback(provider, payloadData)

    // Check if this is an authentication response (has token) or integration response (has provider)
    if (response.token) {
      // Authentication flow - user was not logged in
      await authFlow.handleAuthSuccess(response, provider, response.new_user)
      
      if (!response.new_user) {
        // Handle existing user login
        if (window.opener) {
          try {
            const loginMessage = useWindowMessage(WindowMessageTypes.LOGIN_COMPLETE)
            const providerMessage = useWindowMessage(WindowMessageTypes.OAUTH_PROVIDER_CONNECTED)
            
            await Promise.all([
              loginMessage.send(window.opener, {
                waitForAcknowledgment: true,
                timeout: 500
              }),
              providerMessage.send(window.opener, {
                useMessageChannel: false,
                waitForAcknowledgment: false
              })
            ])
            
            window.close()
            loading.value = false
          } catch (err) {
            console.error("Error in social callback:", err)
            loading.value = false
          }
        } else {
          router.push({ name: "home" })
        }
      } else {
        // Handle new user registration
        router.push({ name: "forms-create" })
        useAlert().success("Success! You're now registered with your Google account! Welcome to OpnForm.")
      }
    } else if (response.provider) {
      // Integration flow - user was already logged in, provider was connected
      if (window.opener) {
        try {
          const providerMessage = useWindowMessage(WindowMessageTypes.OAUTH_PROVIDER_CONNECTED)
          
          await providerMessage.send(window.opener, {
            useMessageChannel: false,
            waitForAcknowledgment: false
          })
          
          // Auto-close if specified in response
          if (response.autoClose) {
            window.close()
          } else {
            useAlert().success(`${response.provider.name} account connected successfully!`)
            loading.value = false
          }
        } catch (err) {
          console.error("Error in integration callback:", err)
          useAlert().success(`${response.provider.name} account connected successfully!`)
          loading.value = false
        }
      } else {
        // No opener, show success and redirect
        useAlert().success(`${response.provider.name} account connected successfully!`)
        router.push({ name: "home" })
      }
    } else {
      throw new Error("Unexpected response format from OAuth callback")
    }
  } catch (error) {
    console.error("Social login error:", error)
    useAlert().error(error.response?._data?.message || "Authentication failed")
    loading.value = false
  }
}

onMounted(() => {
  // Set a timeout to ensure we don't get stuck in loading state
  const timeoutId = setTimeout(() => {
    if (loading.value) {
      loading.value = false
      console.error("Social login timed out")
    }
  }, 10000) // 10 second timeout
  
  handleCallback().finally(() => {
    clearTimeout(timeoutId)
  })
})
</script>