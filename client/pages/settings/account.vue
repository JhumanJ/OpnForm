<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Danger zone</h3>
    <p class="text-gray-600 text-sm mt-2">
      This will permanently delete your entire account. All your forms, submissions and workspaces will be deleted.
    <span class="text-red-500">
      This cannot be undone.
    </span>
    </p>

    <!-- Submit Button -->
    <v-button :loading="loading" class="mt-4" color="red" @click="useAlert().confirm('Do you really want to delete your account?',deleteAccount)">
      Delete account
    </v-button>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter()
const authStore = useAuthStore()
let loading = false

useOpnSeoMeta({
  title: 'Account'
})
definePageMeta({
  middleware: "auth"
})

const deleteAccount = () => {
  loading = true
  opnFetch('/user', {method:'DELETE'}).then(async (data) => {
    loading = false
    useAlert().success(data.message)

    authStore.logout()
    router.push({ name: 'login' })
  }).catch((error) => {
    useAlert().error(error.data.message)
    loading = false
  })
}
</script>
