<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">
      Password
    </h3>
    <small class="text-gray-600">Manage your password.</small>

    <form class="mt-3" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <!-- Password -->
      <text-input native-type="password"
                  name="password" :form="form" label="Password" :required="true"
      />

      <!-- Password Confirmation-->
      <text-input native-type="password"
                  name="password_confirmation" :form="form" label="Confirm Password" :required="true"
      />

      <!-- Submit Button -->
      <v-button :loading="form.busy" class="mt-4">
        Update password
      </v-button>
    </form>
  </div>
</template>

<script setup>
useOpnSeoMeta({
  title: 'Password'
})
definePageMeta({
  middleware: "auth"
})

let form = useForm({
  password: '',
  password_confirmation: ''
})

const update = () => {
  form.patch('/settings/password').then((response) => {
    form.reset()
    useAlert().success('Password updated.')
  }).catch((error) => {
    console.error(error)
  })
}
</script>
