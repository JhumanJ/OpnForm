<template>
  <!--  Forgot password modal  -->
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-lg' }"
    :title="isMailSent ? 'Check your email' : 'Forgot password?'"
    :description="isMailSent ? '' : 'No worries, we\'ll send you reset instructions.'"
  >
    <template #body>
      <template v-if="isMailSent">
        <div class="text-center">
          We sent a password reset link to <br><span class="font-bold">{{ form.email }}</span>
        </div>
        <div class="w-full mt-4 text-center">
          <UButton
            icon="heroicons:arrow-path"
            variant="outline"
            color="neutral"
            label="Resend email"
            @click="send"
          />
        </div>
      </template>
      <template v-else>
        <form
          @submit.prevent="send"
          @keydown="form.onKeydown($event)"
        >
          <text-input
            name="email"
            :form="form"
            label="Email"
            placeholder="Your email address"
            :required="true"
          />

          <div class="w-full mt-6">
            <UButton
              type="submit"
              block
              :loading="form.busy"
              label="Reset password"
            />
          </div>
        </form>
      </template>
    </template>

    <template #footer>
      <UButton
        block
        icon="heroicons:arrow-left"
        variant="link"
        color="neutral"
        @click="close"
        label="Back to log in"
      />
    </template>
  </UModal>
</template>

<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['close'])

const isMailSent = ref(false)
const form = useForm({
  email: "",
})

const isOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      close()
    }
  }
})

const send = () => {
  form.post("/password/email").then(() => {
    isMailSent.value = true
  }).catch(error => {
    if(error?.data?.email){
      useAlert().error(error.data?.email)
      isMailSent.value = false
    }
  })
}

const close = () => {
  emit("close")
  isMailSent.value = false
}
</script>
