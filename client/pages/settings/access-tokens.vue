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
        label="Create new token"
        icon="i-heroicons-plus"
        :loading="loading"
        @click="accessTokenModal = true"
      />
    </div>

    <div
      v-if="loading"
      class="w-full text-blue-500 text-center"
    >
      <Loader class="h-10 w-10 p-5" />
    </div>

    <div class="py-6">
      <SettingsAccessTokenCard
        v-for="token in tokens"
        :key="token.id"
        :token="token"
      />
    </div>

    <SettingsAccessTokenModal
      :show="accessTokenModal"
      @close="accessTokenModal = false"
    />
  </div>
</template>

<script setup>
useOpnSeoMeta({
  title: "Access Tokens",
})

const accessTokenModal = ref(false)
const accessTokenStore = useAccessTokenStore()
const tokens = computed(() => accessTokenStore.getAll)
const loading = computed(() => accessTokenStore.loading)

onMounted(() => {
  accessTokenStore.fetchTokens()
})
</script>
