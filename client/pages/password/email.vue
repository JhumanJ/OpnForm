<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
        <h1 class="my-6">
          Reset password
        </h1>
        <form
          @submit.prevent="send"
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

          <!-- Submit Button -->
          <UButton
            class="w-full"
            :loading="form.busy"
            type="submit"
            label="Send Password Reset Link"
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
      email: "",
    }),
  }),

  methods: {
    async send() {
      const { data } = await this.form.post("/password/email")

      this.status = data.status

      this.form.reset()
    },
  },
}
</script>
