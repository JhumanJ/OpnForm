<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="pb-6 border-b w-full flex flex-col sm:flex-row gap-2">
      <div class="border border-gray-400 rounded-md p-4 w-full mx-auto">
        <div class="mb-2">
          Views
        </div>
         
        <Loader
          v-if="isLoading"
          class="h-6 w-6 text-nt-blue"
        />
        <span
          v-else-if="form.is_pro"
          class="font-semibold text-2xl"
        >
          {{ totalViews }}
        </span>
        <span
          v-else
          class="blur-[3px]"
        >
          123
        </span>
      </div>
      <div class="border border-gray-400 rounded-md p-4 w-full mx-auto">
        <div class="mb-2">
          Submissions
        </div>
         
        <Loader
          v-if="isLoading"
          class="h-6 w-6 text-nt-blue"
        />
        <span
          v-else-if="form.is_pro"
          class="font-semibold text-2xl"
        >
          {{ totalSubmissions }}
        </span>
        <span
          v-else
          class="blur-[3px]"
        >
          123
        </span>
      </div>
      <div class="border border-gray-400 rounded-md p-4 w-full mx-auto">
        <div class="mb-2">
          Completion Rate
        </div>
         
        <Loader
          v-if="isLoading"
          class="h-6 w-6 text-nt-blue"
        />
        <span
          v-else-if="form.is_pro"
          class="font-semibold text-2xl"
        >
          {{ completionRate }}%
        </span>
        <span
          v-else
          class="blur-[3px]"
        >
          100%
        </span>
      </div>
      <div class="border border-gray-400 rounded-md p-4 w-full mx-auto">
        <div class="mb-2">
          Average Duration
        </div>
         
        <Loader
          v-if="isLoading"
          class="h-6 w-6 text-nt-blue"
        />
        <span
          v-else-if="form.is_pro"
          class="font-semibold text-2xl"
        >
          {{ averageDuration }}
        </span>
        <span
          v-else
          class="blur-[3px]"
        >
          10 seconds
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
      completionRate.value = responseData.completion_rate ?? 0
      averageDuration.value = responseData.average_duration ?? '-'
      isLoading.value = false
    }
  })
}
</script>
