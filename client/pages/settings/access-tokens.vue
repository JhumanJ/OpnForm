<template>
  <div>
    <div class="flex items-center gap-y-4 flex-wrap-reverse">
      <div class="flex-grow">
        <h3 class="font-semibold text-2xl text-gray-900">
          Access Tokens
        </h3>
        <small class="text-gray-600">Manage your access tokens keys.</small>
      </div>

      <UButton
        label="API Docs"
        icon="i-heroicons-book-open"
        variant="ghost"
        :to="opnformConfig.links.api_docs"
        target="_blank"
        class="mr-2"
      />
      
      <UButton
        label="Create new token"
        icon="i-heroicons-plus"
        :loading="loading"
        :disabled="!user.is_pro"
        @click="accessTokenModal = true"
      />
    </div>

    <UAlert
      v-if="!user.is_pro"
      icon="i-heroicons-user-group-20-solid"
      class="my-4 !text-orange-500"
      color="orange"
      variant="subtle"
      title="Pro plan required"
    >
      <template #description>
        Please <a
          href="#"
          class="text-orange-500 underline"
          @click.prevent="openSubscriptionModal"
        >
          upgrade your account
        </a> to create and manage access tokens.
      </template>
    </UAlert>

    <div
      v-if="loading"
      class="w-full text-blue-500 text-center"
    >
      <Loader class="h-10 w-10 p-5" />
    </div>

    <div class="py-6">
      <AccessTokenCard
        v-for="token in tokens"
        :key="token.id"
        :token="token"
      />
    </div>

    <AccessTokenModal
      :show="accessTokenModal"
      @close="accessTokenModal = false"
    />
  </div>
</template>

<script setup>
import opnformConfig from '~/opnform.config.js'
useOpnSeoMeta({
  title: "Access Tokens",
})

const accessTokenModal = ref(false)
const accessTokenStore = useAccessTokenStore()
const tokens = computed(() => accessTokenStore.getAll)
const loading = computed(() => accessTokenStore.loading)
const user = computed(() => useAuthStore().user)
const subscriptionModalStore = useSubscriptionModalStore()

const openSubscriptionModal = () => {
  subscriptionModalStore.setModalContent('Upgrade to start using our API')
  subscriptionModalStore.openModal()
}

onMounted(() => {
  if (user.value.is_pro) {
    accessTokenStore.fetchTokens()
  }
})
</script>
