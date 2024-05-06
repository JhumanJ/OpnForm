<template>
  <AdminCard
    v-if="props.user.stripe_id"
    title="Extend trial"
    icon="heroicons:calendar-16-solid"
  >
    <form
      class="space-y-6 flex flex-col justify-between"
      @submit.prevent="extendTrial"
    >
      <p class="text-xs text-gray-500">
        You can extend the trial of subscribers that are still in the trial
        period. Usually, you should not offer more than 7 days of trial, but
        you can add up to 14 days if needed.
      </p>
      <div>
        <text-input
          name="number_of_day"
          :form="form"
          label="Number of days"
          native-type="day"
          :required="true"
          help="Number Of Days"
          placeholder="7"
        />
        <v-button
          :loading="loading"
          type="success"
          class="w-full"
          color="white"
        >
          Apply Extend Trial
        </v-button>
      </div>
    </form>
  </AdminCard>
</template>

<script setup>
const props = defineProps({
  user: { type: Object, required: true }
})

let loading = ref(false)
const form = useForm({
  user_id: props.user.id,
  number_of_day: ''
})

const extendTrial = () => {
  if (!props.user.stripe_id) return
  loading = true
  form
    .patch('/moderator/extend-trial')
    .then(async (data) => {
      loading = false
      useAlert().success(data.message)
    })
    .catch((error) => {
      useAlert().error(error.data.message)
      loading = false
    })
}

</script>
