<template>
  <UButton
    icon="i-heroicons-eye-16-solid"
    :loading="form.busy"
    @click="impersonate"
    label="Impersonate User"
  />
</template>

<script setup>

import { useQueryClient } from '@tanstack/vue-query'

const props = defineProps({
  user: { type: Object, required: true }
})

const authStore = useAuthStore()
const queryClient = useQueryClient()

const form = useForm({
  user_id: props.user.id
})

const { user } = useAuth()
const { data: userData } = user()

const impersonate = () => {
  authStore.startImpersonating()
  form.post(`/moderator/impersonate/${props.user.id}`).then(async (data) => {
    // Save the token with its expiration time.
    authStore.setToken(data.token, data.expires_in)
    await queryClient.invalidateQueries()

    useAlert().success(`Impersonating ${userData.value.name}`)
    useRouter().push({ name: 'home' })
  })
    .catch((error) => {
      useAlert().error(error.data.message)
    })
}
</script>
