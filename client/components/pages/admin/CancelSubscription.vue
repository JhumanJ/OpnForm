<template>
  <AdminCard
    v-if="props.user.stripe_id"
    title="Cancel subscription"
    icon="heroicons:trash-16-solid"
  >
    <form
      class="space-y-6 flex flex-col h-full justify-between"
      @submit.prevent="askCancel"
    >
      <p class="text-xs text-gray-500">
        Ideally customers should cancel subscription themselves via the UI. If
        you cancel the subscription for them, please provide a reason.
      </p>
      <div>
        <text-input
          name="cancellation_reason"
          :form="form"
          label="Cancellation reason"
          native-type="reason"
          :required="true"
          help="Cancellation reason"
        />

        <v-button
          :loading="loading"
          type="success"
          class="w-full"
          color="outline-red"
        >
          <Icon
            class="inline w-4 h-4 text-red-600"
            name="heroicons:exclamation-triangle-16-solid"
          />
          Cancel subscription now
        </v-button>
      </div>
    </form>
  </AdminCard>
</template>

<script setup>
const props = defineProps({
  user: { type: Object, required: true }
})

const alert = useAlert()
let loading = ref(false)
const form = useForm({
  user_id: props.user.id,
  cancellation_reason: ''
})

const askCancel = () => {
  alert.confirm('Are you sure? This will cancel the subscription for this user.', cancelSubscription)
}

const cancelSubscription = () => {
  if (!props.user.stripe_id) return
  loading = true
  form
    .patch('/moderator/cancellation-subscription')
    .then(async (data) => {
      loading = false
      alert.success(data.message)
    })
    .catch((error) => {
      alert.error(error.data.message)
      loading = false
    })
}

</script>
