<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">
      Profile details
    </h3>
    <small class="text-gray-600">Update your username and manage your account details.</small>

    <form class="mt-3" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <!-- Name -->
      <text-input name="name" :form="form" label="Name" :required="true" />

      <!-- Email -->
      <text-input name="email" :form="form" label="Email" :required="true" />

      <!-- Submit Button -->
      <v-button :loading="form.busy" class="mt-4">
        Save changes
      </v-button>
    </form>
  </div>
</template>

<script setup>
const authStore = useAuthStore()
const user = computed(() => authStore.user)

useOpnSeoMeta({
  title: 'Profile'
})
definePageMeta({
  middleware: "auth"
})

let form = useForm({
  name: '',
  email: ''
})

const update = () => {
  form.patch('/settings/profile').then((response) => {
    authStore.updateUser(response)
    useAlert().success('Your info has been updated!')
  })
}

onBeforeMount(() => {
  // Fill the form with user data.
  form.keys().forEach(key => {
    form[key] = user.value[key]
  })
})
</script>
