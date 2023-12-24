<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">
      Profile details
    </h3>
    <small class="text-gray-600">Update your username and manage your account details.</small>

    <form class="mt-3" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success class="mb-5" :form="form" message="Your info has been updated!" />

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

<script>
export default {
  scrollToTop: false,

  setup () {
    const authStore = useAuthStore()
    return {
      authStore,
      user : computed(() => authStore.user)
    }
  },

  data: () => ({
    metaTitle: 'Profile',
    form: useForm({
      name: '',
      email: ''
    })
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.authStore.updateUser(data)
    }
  }
}
</script>
