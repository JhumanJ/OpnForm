<template>
  <div class=" bg-gray-50 flex flex-col justify-center sm:px-6 lg:px-8 py-10 flex-grow">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
        Welcome to OpnForm!
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        You're using the self-hosted version of OpnForm and need to set up your account.
        Please enter your email and create a password to continue.
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-sm sm:rounded-sm sm:px-10">
        <form
          @submit.prevent="updateCredentials"
          @keydown="form.onKeydown($event)"
        >
          <!-- Email -->
          <text-input
            name="email"
            :form="form"
            label="Email"
            :required="true"
            placeholder="Your email address"
          />

          <!-- Password -->
          <text-input
            native-type="password"
            placeholder="Your password"
            name="password"
            :form="form"
            label="Password"
            :required="true"
          />

          <!-- Password Confirmation-->
          <text-input
            native-type="password"
            :form="form"
            :required="true"
            placeholder="Enter confirm password"
            name="password_confirmation"
            label="Confirm Password"
          />

          <!-- Submit Button -->
          <div class="mt-6">
            <v-button
              class="w-full justify-center"
              :loading="form.busy || loading"
            >
              Update Credentials
            </v-button>
          </div>

          <!-- Cancel Link -->
          <div class="mt-4 text-center">
            <button 
              type="button" 
              class="text-sm text-gray-600 hover:text-gray-900"
              @click="logout"
            >
              Cancel and return to login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue"

const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const formsStore = useFormsStore()
const user = computed(() => authStore.user)
const router = useRouter()
const loading = ref(false)
const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  agree_terms: false,
  appsumo_license: null,
})

onMounted(() => {
  form.email = user?.value?.email
})

const { list: fetchWorkspaces } = useWorkspaces()

const updateCredentials = () => {
  loading.value = true
  form
    .post("update-credentials")
    .then(async (data) => {
      authStore.setUser(data.user)
      const { data: workspacesData } = await fetchWorkspaces()
      workspacesStore.set(workspacesData.value)
      formsStore.loadAll(workspacesStore.currentId)
      router.push({ name: "home" })
    })
    .catch((error) => {
      console.error(error)
      useAlert().error(error.response._data.message)
    })
    .finally(() => {
      loading.value = false
    })
}

const logout = () => {
  authStore.logout()
  router.push({ name: "login" })
}
</script>