<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
        <h1 class="my-6">
          Reset Password
        </h1>
        <form
          @submit.prevent="reset"
          @keydown="form.onKeydown($event)"
        >
          <UAlert
            v-if="status"
            color="success"
            variant="subtle"
            :description="status"
            icon="i-heroicons-check-circle"
            class="mb-4"
          />

          <!-- Email -->
          <text-input
            name="email"
            :form="form"
            label="Email"
            :required="true"
          />

          <!-- Password -->
          <text-input
            native-type="password"
            name="password"
            :form="form"
            label="Password"
            :required="true"
          />

          <!-- Password Confirmation-->
          <text-input
            native-type="password"
            class="mb-5"
            name="password_confirmation"
            :form="form"
            label="Confirm Password"
            :required="true"
          />

          <!-- Submit Button -->
          <UButton
            class="w-full"
            :loading="form.busy"
            type="submit"
            label="Reset Password"
          />
        </form>
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
export default {
  setup() {
    definePageMeta({
      middleware: "guest",
    })
    useOpnSeoMeta({
      title: "Reset Password",
    })
  },

  data: () => ({
    status: "",
    form: useForm({
      token: "",
      email: "",
      password: "",
      password_confirmation: "",
    }),
  }),

  created() {
    this.form.email = this.$route.query.email
    this.form.token = this.$route.params.token
  },

  methods: {
    async reset() {
      
      const data  = await this.form.post("/password/reset")
      this.status = data.status

      this.form.reset()
    },
  },
}
</script>
