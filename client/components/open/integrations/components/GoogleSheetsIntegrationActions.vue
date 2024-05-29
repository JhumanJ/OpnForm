<template>
  <div class="flex flex-1 items-center">
    <div class="space-y-1">
      <div class="font-medium mr-2">
        {{ integration.provider.user.name }}
      </div>
      <div class="text-sm">
        {{ integration.provider.user.email }}
      </div>
    </div>

    <div class="ml-auto">
      <v-button
        :href="integration.data.url"
        target="_blank"
        color="white"
      >
        Open spreadsheet
      </v-button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  integration: Object,
})
</script>
