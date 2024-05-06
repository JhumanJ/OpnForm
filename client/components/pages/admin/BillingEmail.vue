<template>
  <AdminCard
    v-if="props.user.stripe_id"
    title="Billing email"
    icon="heroicons:envelope-16-solid"
  >
    <p class="text-xs text-gray-500">
      You can update the billing email of the subscriber.
    </p>
    <div
      v-if="loading"
      class="text-gray-600 dark:text-gray-400"
    >
      <Loader class="h-6 w-6 mx-auto m-10" />
    </div>
    <form
      v-else
      class="mt-6 space-y-6 flex flex-col justify-between"
      @submit.prevent="updateUserBillingEmail"
    >
      <div>
        <text-input
          name="billing_email"
          :form="form"
          label="Billing email"
          native-type="email"
          :required="true"
          help="Billing email"
          placeholder="Billing email"
          :disabled="!userCreated"
        />
        <v-button
          :loading="loading"
          type="success"
          class="w-full"
          color="white"
          :disabled="!userCreated"
        >
          Update billing email
        </v-button>
      </div>
    </form>
  </AdminCard>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true }
})

const loadingBillingEmail = ref(false)
const loading = ref(false)
const userCreated = ref(false)
const form = useForm({
    billing_email: '',
    user_id: props.user.id
})

onMounted(() => {
  if (!props.user.stripe_id) return
    loadingBillingEmail.value = true
    opnFetch("/moderator/billing/" + props.user.id + "/email",).then(data => {
        loadingBillingEmail.value = false
        userCreated.value = true
        form.billing_email = data.billing_email
    }).catch(error => {
        loadingBillingEmail.value = false
        userCreated.value = false
    })
})

const updateUserBillingEmail = () => {
    loading.value = true
    form.patch("/moderator/billing/email")
        .then(async (data) => {
            loading.value = false
            useAlert().success(data.message)
        })
        .catch((error) => {
            useAlert().error(error.data.message)
            loading.value = false
        })
}
</script>
