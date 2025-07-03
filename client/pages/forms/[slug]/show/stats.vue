<template>
  <div class="p-4">
    <div class="w-full max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-2">
      <div
        v-for="(stat, index) in [
          { label: 'Views', value: totalViews, placeholder: '123' },
          { label: 'Submissions', value: totalSubmissions, placeholder: '123' },
          { label: 'Completion', value: completionRate + '%', placeholder: '100%' },
          { label: 'Avg. Duration', value: averageDuration, placeholder: '10 seconds' }
        ]"
        :key="index"
        class="border border-gray-300 rounded-lg shadow-xs p-4"
      >
        <div class="mb-2 text-xs text-gray-500">
          {{ stat.label }}
        </div>
         
        <USkeleton
          v-if="isLoading"
          class="h-6 w-16"
        />
        <span
          v-else-if="form.is_pro"
          class="font-medium text-xl"
        >
          {{ stat.value }}
        </span>
        <span
          v-else
          class="blur-[3px]"
        >
          {{ stat.placeholder }}
        </span>
      </div>
    </div>

    <form-stats class="w-full max-w-4xl mx-auto" :form="form" />
  </div>
</template>

<script setup>
import FormStats from "~/components/open/forms/components/FormStats.vue"
import { formsApi } from "~/api"

const props = defineProps({
  form: { type: Object, required: true },
})

definePageMeta({
  middleware: "auth",
})
useOpnSeoMeta({
  title: props.form ? "Form Analytics - " + props.form.title : "Form Analytics",
})

const isLoading = ref(false)
const totalViews = ref(0)
const totalSubmissions = ref(0)
const completionRate = ref(0)
const averageDuration = ref('-')

onMounted(() => {
  getCardData()
})

const getCardData = async() => {
  if (!props.form || !props.form.is_pro) { return null }
  isLoading.value = true
  formsApi.statsDetails(props.form.workspace_id, props.form.id).then((responseData) => {
    if (responseData) {
      totalViews.value = responseData.views ?? 0
      totalSubmissions.value = responseData.submissions ?? 0
      completionRate.value = Math.min(100,responseData.completion_rate ?? 0)
      averageDuration.value = responseData.average_duration ?? '-'
      isLoading.value = false
    }
  })
}
</script>
