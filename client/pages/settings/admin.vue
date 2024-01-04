<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Admin settings</h3>
    <small class="text-gray-600">Manage settings.</small>


    <h3 class="mt-3 text-lg font-semibold mb-4">
      Tools
    </h3>
    <div class="flex flex-wrap mb-5">
      <a href="/stats">
        <v-button class="mx-1" color="gray" shade="lighter">
          Stats
        </v-button>
      </a>
      <a href="/horizon">
        <v-button class="mx-1" color="gray" shade="lighter">
          Horizon
        </v-button>
      </a>
    </div>
    <h3 class="text-lg font-semibold mb-4">
      Impersonate User
    </h3>
    <form @submit.prevent="impersonate" @keydown="form.onKeydown($event)">
      <!-- Password -->
      <text-input name="identifier" :form="form" label="Identifier"
                  :required="true" help="User Id, User Email or Form Slug"
      />

      <!-- Submit Button -->
      <v-button :loading="loading" class="mt-4">Impersonate User</v-button>
    </form>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

definePageMeta({
  middleware: "admin"
})

useSeoMeta({
  title: 'Admin'
})

const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const router = useRouter()
let form = useForm({
  identifier: ''
})
let loading = false

const impersonate = () => {
  loading = true
  authStore.startImpersonating()
  opnFetch('/admin/impersonate/' + encodeURI(form.identifier)).then(async (data) => {
    loading = false

    // Save the token.
    authStore.saveToken(data.token, false)

    // Fetch the user.
    await authStore.fetchUser()

    // Redirect to the dashboard.
    workspacesStore.set([])
    router.push({ name: 'home' })
  }).catch((error) => {
    useAlert().error(error.response.data.message)
    loading = false
  })
}
</script>
