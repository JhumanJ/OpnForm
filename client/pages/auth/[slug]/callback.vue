<template>
  <div class="flex flex-grow mt-6 mb-10">
    <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
      <div
        v-if="loading"
        class="m-10"
      >
        <h3 class="my-6 text-center">
          Completing sign in...
        </h3>
        <Loader class="h-6 w-6 mx-auto m-10" />
      </div>
      <div
        v-else-if="error"
        class="m-6 flex flex-col items-center space-y-4"
      >
        <UAlert
          icon="i-lucide-alert-triangle"
          color="error"
          variant="subtle"
          class="w-full"
          :description="error"
        />
        <UButton
          :to="{ name: 'login' }"
          label="Back to login"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { oidcApi } from "~/api"

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const error = ref(null)
const authFlow = useAuthFlow()

const handleCallback = async () => {
  const slug = route.params.slug
  
  try {
    // Build query params object from route
    const queryParams = {}
    Object.keys(route.query).forEach(key => {
      if (route.query[key]) {
        queryParams[key] = route.query[key]
      }
    })
    
    // Call the OIDC callback endpoint to process authorization code
    const response = await oidcApi.callback(slug, queryParams)
    
    // Response should contain token and user
    if (response.token && response.user) {
      // Handle authentication success (response already has the right structure)
      await authFlow.handleAuthSuccess(response, 'oidc', response.new_user)
      
      // Redirect based on user status (aligned with OAuth callback pattern)
      if (response.new_user) {
        router.push({ name: "forms-create" })
        useAlert().success("Success! You're now registered with OIDC. Welcome to OpnForm.")
      } else {
        // For existing users, redirect to intended URL or home (aligned with OAuth)
        router.push({ name: "home" })
        useAlert().success("Successfully signed in!")
      }
    } else {
      throw new Error("Unexpected response format from OIDC callback")
    }
  } catch (err) {
    console.error("[OIDC Callback] Authentication error:", err)
    
    const errorMessage = err.response?._data?.message || err.message || "Authentication failed"
    error.value = errorMessage
    
    // Handle specific error cases
    if (errorMessage.includes('account with this email already exists')) {
      error.value = 'An account with this email already exists. Please contact your administrator to link your accounts.'
    } else if (errorMessage.includes('blocked')) {
      error.value = 'Your account has been blocked. Please contact support.'
    }
    
    loading.value = false
  }
}

onMounted(() => {
  // Set a timeout to ensure we don't get stuck in loading state
  const timeoutId = setTimeout(() => {
    if (loading.value) {
      loading.value = false
      error.value = 'Authentication timed out. Please try again.'
    }
  }, 10000) // 10 second timeout
  
  handleCallback().finally(() => {
    clearTimeout(timeoutId)
  })
})
</script>

