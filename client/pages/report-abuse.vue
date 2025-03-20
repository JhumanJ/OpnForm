<template>
  <div>
    <div
      class="h-screen w-screen mt-4"
      style="height: 100%;"
    >
      <iframe
        id="opnform-form-abuse-report-wnociq"
        style="border:none;width:100%;min-height: 1000px;"
        src="https://opnform.com/forms/opnform-form-abuse-report-wnociq"
      />
    </div>
    <open-form-footer />
  </div>
</template>

<script setup>
useOpnSeoMeta({
  title: "Report Abuse",
})
definePageMeta({
  middleware: ["self-hosted"]
})
defineRouteRules({
  swr: 3600,
})

useHead({
  script: [
    {
      src: 'https://opnform.com/widgets/iframe.min.js',
      onload: "initEmbed('opnform-form-abuse-report-wnociq')"
    }
  ]
})
</script>
