<template>
  <UButton
    size="sm"
    color="white"
    icon="i-heroicons-key-16-solid"
    :loading="loading"
    @click="resetPassword"
  >
    Reset Password
  </UButton>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true }
})

const loading = ref(false)
const form = useForm({
  user_id: props.user.id
})

const resetPassword = ()=>{
    return useAlert().confirm(
        "Are you sure you want to send a password reset email?",
        () => {
            loading.value = true
            form
                .patch('/moderator/send-password-reset-email')
                .then(async (data) => {
                    loading.value = false
                    useAlert().success(data.message)
                })
                .catch((error) => {
                    useAlert().error(error.data.message)
                    loading.value = false
                })
        })
}
</script>