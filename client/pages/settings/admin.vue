<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Admin settings</h3>
    <small class="text-gray-600">Manage settings.</small>


    <h3 class="mt-3 text-lg font-semibold mb-4">
      Tools
    </h3>
    <div class="flex flex-wrap mb-5">
      <a :href="statsUrl" target="_blank">
        <v-button class="mx-1" color="gray" shade="lighter">
          Stats
        </v-button>
      </a>
      <a :href="horizonUrl" target="_blank">
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
import {opnFetch} from "~/composables/useOpnApi.js";
import {fetchAllWorkspaces} from "~/stores/workspaces.js";

definePageMeta({
  middleware: "moderator"
})

useOpnSeoMeta({
  title: 'Admin'
})

const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const router = useRouter()
let form = useForm({
  identifier: ''
})
const loading = ref(false)

const runtimeConfig = useRuntimeConfig()
const statsUrl = runtimeConfig.public.apiBase + '/stats'
const horizonUrl = runtimeConfig.public.apiBase + '/horizon'

const impersonate = () => {
  loading.value = true
  authStore.startImpersonating()
  opnFetch('/admin/impersonate/' + encodeURI(form.identifier)).then(async (data) => {
    // Save the token.
    authStore.setToken(data.token, false)

    // Fetch the user.
    const userData = await opnFetch('user')
    authStore.setUser(userData)
    const workspaces = await fetchAllWorkspaces()
    workspacesStore.set(workspaces.data.value)
    loading.value = false

    router.push({ name: 'home' })
  }).catch((error) => {
    console.error(error)
    useAlert().error(error.data.message)
    loading.value = false
  })
}
</script>
