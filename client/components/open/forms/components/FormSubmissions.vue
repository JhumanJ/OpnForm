<template>
  <div
    id="table-page"
    class="w-full flex flex-col"
  >
    <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4 pt-4">
      <h3 class="font-semibold text-xl">
        Submissions
      </h3>

      <OpenTable
        v-if="form"
        ref="table"
        class="border"
        :data="tableData"
        :loading="isLoading"
        @deleted="onDeleteRecord"
        @updated="(submission)=>onUpdateRecord(submission)"
      />

      <Loader
        v-else
        class="h-6 w-6 text-blue-500 mx-auto"
      />
      
    </div>
  </div>
</template>

<script setup>
import OpenTable from '~/components/open/tables/OpenTable.vue'

const workingFormStore = useWorkingFormStore()
const recordStore = useRecordsStore()
const form = storeToRefs(workingFormStore).content
const tableData = storeToRefs(recordStore).getAll
const route = useRoute()
const slug = route.params.slug

const fullyLoaded = ref(false)
const table = ref(null)

const isLoading = computed(() => {
  return recordStore.loading
})

onMounted(() => {
  onFormChange()
})

watch(() => form.value?.id, () => {
  onFormChange()
})

const onFormChange = () => {
  if (form.value === null || form.value.slug !== slug) {
    return
  }
  fullyLoaded.value = false
  getSubmissionsData()
}

const getSubmissionsData = () => {
  if (fullyLoaded.value) {
    return
  }

  recordStore.startLoading()
  opnFetch('/open/forms/' + form.value.id + '/submissions?page=1').then((firstResponse) => {
    recordStore.save(firstResponse.data.map((record) => record.data))
    
    const lastPage = firstResponse.meta.last_page
    
    if (lastPage > 1) {
      // Create an array of promises for remaining pages
      const remainingPages = Array.from({ length: lastPage - 1 }, (_, i) => {
        const page = i + 2 // Start from page 2
        return opnFetch('/open/forms/' + form.value.id + '/submissions?page=' + page)
      })
      
      // Fetch all remaining pages in parallel
      return Promise.all(remainingPages)
    }
    return []
  }).then(responses => {
    // Save all responses data
    responses.forEach(response => {
      recordStore.save(response.data.map((record) => record.data))
    })
    
    fullyLoaded.value = true
    recordStore.stopLoading()
  }).catch(() => {
    recordStore.stopLoading()
  })
}


const onUpdateRecord = (submission) => {
  recordStore.save(submission)
}

const onDeleteRecord = (submission) => {
  recordStore.remove(submission)
}

</script>
