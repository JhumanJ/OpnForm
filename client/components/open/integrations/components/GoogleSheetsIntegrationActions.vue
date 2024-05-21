<template>
  <v-button
    :href="integration.data.url"
    target="_blank"
    color="white"
  >
    Open spreadsheet
  </v-button>
</template>

<script setup>
defineProps({
  integration: Object,
})
</script>
