<template>
  <AdminCard
    v-if="props.user.stripe_id"
    title="Apply discount"
    icon="heroicons:tag-20-solid"
  >
    <form
      class="space-y-6 flex flex-col justify-between"
      @submit.prevent="applyDiscount"
    >
      <p class="text-xs text-gray-500">
        This is only for students, academics and NGOs. Make sure to verify
        their status before applying discount (student/university email, NGO
        website, proof of non-profit, etc). They need to create their
        subscriptions before you can apply the 40% discount.
      </p>
      <v-button
        :loading="loading"
        type="success"
        class="w-full"
        color="white"
      >
        Apply Discount
      </v-button>
    </form>
  </AdminCard>
</template>

<script setup>
const props = defineProps({
  user: { type: Object, required: true }
})

let loading = ref(false)
const form = useForm({
  user_id: props.user.id
})

const applyDiscount = () => {
  if (!props.user.stripe_id) return
  loading = true
  form
    .patch('/moderator/apply-discount')
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
