<template>
  <div class="space-y-8">
    <!-- Password Section -->
    <div class="space-y-4">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Change Password</h3>
        <p class="text-sm text-neutral-500 mt-1">
          Update your password to keep your account secure.
        </p>
      </div>

      <VForm size="sm">
        <form
          @submit.prevent="updatePassword"
        >
          <div class="max-w-sm">
            <text-input
              :form="passwordForm"
              name="password"
              label="New Password"
              native-type="password"
              placeholder="Enter new password"
              :required="true"
            />
            
            <text-input
              :form="passwordForm"
              name="password_confirmation"
              label="Confirm Password"
              native-type="password"
              placeholder="Confirm new password"
              :required="true"
            />
          </div>

          <div class="mt-4">
            <UButton
              type="submit"
              :loading="passwordForm.busy"
              color="primary"
            >
              Update Password
            </UButton>
          </div>
        </form>
      </VForm>
    </div>

    <!-- Two-Factor Authentication (Future Enhancement) -->
    <div class="pt-8 border-t border-neutral-200">
      <div class="flex flex-col gap-2 items-start">
        <div>
          <h3 class="text-lg font-medium text-neutral-900">Two-Factor Authentication</h3>
          <p class="text-sm text-neutral-500 mt-1">
            Add an extra layer of security to your account.
          </p>
        </div>
        
        <UButton
          color="primary"
          disabled
        >
          Coming Soon
        </UButton>
        
      </div>
    </div>
  </div>
</template>

<script setup>
const alert = useAlert()

// Password form
const passwordForm = useForm({
  password: '',
  password_confirmation: ''
})

// Update password
const updatePassword = () => {
  passwordForm
    .patch('/settings/password')
    .then(() => {
      passwordForm.reset()
      alert.success('Password updated.')
    })
    .catch((error) => {
      console.error(error)
    })
}
</script> 