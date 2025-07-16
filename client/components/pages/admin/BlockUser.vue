<template>
  <AdminCard
    :title="isBlocked ? 'Unblock User' : 'Block User'"
    :icon="isBlocked ? 'heroicons:lock-open-20-solid' : 'heroicons:no-symbol-20-solid'"
  >
    <div class="space-y-6 flex flex-col justify-between">
      <p class="text-xs text-neutral-500">
        <template v-if="isBlocked">
          This will unblock the user and allow them to log in again. Their forms will remain in draft status.
          <br>
          <b>Blocked on:</b> {{ new Date(user.meta.blocked_at).toLocaleString() }}
          <br>
          <b>Reason:</b> {{ user.meta.block_reason }}
        </template>
        <template v-else>
          This will block the user from accessing their account and set all their forms to draft.
        </template>
      </p>

      <VForm @submit.prevent="submit">
        <TextAreaInput
          label="Reason"
          name="reason"
          :form="form"
          :required="true"
        />
        <UButton
          :loading="loading"
          type="submit"
          class="mt-4"
          block
          :label="isBlocked ? 'Unblock User' : 'Block User'"
        />
      </VForm>
    </div>
  </AdminCard>
</template>

<script setup>
import { adminApi } from '~/api'

const props = defineProps({
  user: { type: Object, required: true }
})
const emit = defineEmits(['user-updated'])

const alert = useAlert()
const loading = ref(false)

const form = useForm({
  user_id: props.user.id,
  reason: ''
})

const isBlocked = computed(() => {
  return props.user.meta && props.user.meta.blocked_at
})

async function submit() {
  loading.value = true
  try {
    let response
    if (isBlocked.value) {
      response = await adminApi.unblockUser(form.data())
    } else {
      response = await adminApi.blockUser(form.data())
    }
    alert.success(response.message)
    emit('user-updated', response.user)
    form.reset()
  } catch (error) {
    alert.error(error.data?.message || 'An error occurred.')
  } finally {
    loading.value = false
  }
}
</script> 