<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="w-full flex flex-col sm:flex-row gap-2">
      <div
        v-for="(stat, index) in [
          { label: 'Views', value: totalViews, placeholder: '123' },
          { label: 'Submissions', value: totalSubmissions, placeholder: '123' },
          { label: 'Completion Rate', value: completionRate + '%', placeholder: '100%' },
          { label: 'Average Duration', value: averageDuration, placeholder: '10 seconds' }
        ]"
        :key="index"
        class="border border-gray-300 rounded-lg shadow-sm p-4 w-full mx-auto"
      >
        <div class="mb-2 text-sm text-gray-500">
          {{ stat.label }}
        </div>
         
        <Loader
          v-if="isLoading"
          class="h-6 w-6 text-nt-blue"
        />
        <span
          v-else-if="form.is_pro"
          class="font-medium text-2xl"
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

    <form-stats :form="form" />
  </div>
</template>

<script setup>
import FormStats from "~/components/open/forms/components/FormStats.vue"

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
  opnFetch(
      "/open/workspaces/" +
      props.form.workspace_id +
      "/form-stats-details/" +
      props.form.id,
  ).then((responseData) => {
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
