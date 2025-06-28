<template>
  <div class="space-y-8">
    <!-- Profile Information Section -->
    <div class="space-y-4">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Profile Information</h3>
        <p class="text-sm text-neutral-500 mt-1">
          Update your account profile information and email address.
        </p>
      </div>

      <VForm size="sm">
        <form
          @submit.prevent="updateProfile"
          @keydown="profileForm.onKeydown($event)"
        >
          <div class="max-w-sm">
            <text-input
              :form="profileForm"
              name="name"
              label="Full Name"
              placeholder="Enter your full name"
              :required="true"
            />
            <text-input
              :form="profileForm"
              name="email"
              label="Email Address"
              type="email"
              placeholder="Enter your email"
              :required="true"
            />
          </div>

          <div class="mt-4">
            <UButton
              type="submit"
              :loading="profileForm.busy"
              color="primary"
            >
              Save Changes
            </UButton>
          </div>
        </form>
      </VForm>
    </div>

    <div class="pt-8 border-t border-neutral-200">
      <div class="flex flex-col gap-2 items-start">
        <div>
          <h4 class="font-medium text-red-800">Delete Account</h4>
          <p class="mt-1 text-sm text-neutral-500">
            This will permanently delete your entire account. This cannot be undone.
          </p>
        </div>
        
          <UButton
            color="error"
            :loading="deleteLoading"
            @click="confirmDeleteAccount"
          >
            Delete Account
          </UButton>
        
      </div>
    </div>
  </div>
</template>

<script setup>
const authStore = useAuthStore()
const router = useRouter()
const alert = useAlert()

const user = computed(() => authStore.user)

// Profile form
const profileForm = useForm({
  name: '',
  email: ''
})

// Delete account loading state
const deleteLoading = ref(false)

// Update profile
const updateProfile = () => {
  profileForm.patch('/settings/profile').then((response) => {
    authStore.updateUser(response)
    alert.success('Your info has been updated!')
  })
}

// Delete account confirmation
const confirmDeleteAccount = () => {
  alert.confirm(
    'Do you really want to delete your account?',
    deleteAccount
  )
}

// Delete account
const deleteAccount = () => {
  deleteLoading.value = true
  opnFetch('/user', { method: 'DELETE' })
    .then(async (data) => {
      deleteLoading.value = false
      alert.success(data.message)
      authStore.logout()
      router.push({ name: 'login' })
    })
    .catch((error) => {
      alert.error(error.data.message)
      deleteLoading.value = false
    })
}

// Initialize form with user data
onBeforeMount(() => {
  if (user.value) {
    profileForm.keys().forEach((key) => {
      profileForm[key] = user.value[key]
    })
  }
})

// Watch for user changes
watch(user, (newUser) => {
  if (newUser) {
    profileForm.keys().forEach((key) => {
      profileForm[key] = newUser[key]
    })
  }
}, { immediate: true })
</script> 