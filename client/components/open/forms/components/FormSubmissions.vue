<template>
    <div class="w-full">
      <VTransition name="fade">
      <OpenTable
        v-if="form"
        ref="table"
        :data="tableData"
        :loading="isLoading"
        :form="form"
        @deleted="onDeleteRecord"
        @updated="(submission)=>onUpdateRecord(submission)"
      />

      <!-- Submissions Table Skeleton -->
      <div v-else class="overflow-hidden mt-4">
        <!-- Table Header Skeleton -->
        <div class="bg-neutral-50 border-y border-neutral-200 px-4 py-3">
          <div class="flex gap-4">
            <USkeleton class="h-4 w-32" />
            <USkeleton class="h-4 w-24" />
            <USkeleton class="h-4 w-28" />
            <USkeleton class="h-4 w-20" />
          </div>
        </div>
        
        <!-- Table Rows Skeleton -->
        <div v-for="i in ['opacity-100', 'opacity-90', 'opacity-80', 'opacity-70', 'opacity-60', 'opacity-50', 'opacity-40', 'opacity-30', 'opacity-20', 'opacity-10']" :key="i" class="border-b border-neutral-200 px-4 py-3" :class="i">
          <div class="flex gap-4 items-center">
            <USkeleton class="h-4 w-32" />
            <USkeleton class="h-4 w-24" />
            <USkeleton class="h-4 w-28" />
            <USkeleton class="h-4 w-20" />
          </div>
        </div>
      </div>
      </VTransition>
    </div>
</template>

<script setup>
import OpenTable from '~/components/open/tables/OpenTable.vue'
import { formsApi } from '~/api/forms'

const props = defineProps({
  form: { type: Object, required: true },
})

const recordStore = useRecordsStore()
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

watch(() => props.form?.id, () => {
  onFormChange()
})

const onFormChange = () => {
  if (props.form === null || props.form.slug !== slug) {
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
  formsApi.submissions.list(props.form.id, { query: { page: 1 } }).then((firstResponse) => {
    recordStore.save(firstResponse.data.map((record) => record.data))
    
    const lastPage = firstResponse.meta.last_page
    
    if (lastPage > 1) {
      // Create an array of promises for remaining pages
      const remainingPages = Array.from({ length: lastPage - 1 }, (_, i) => {
        const page = i + 2 // Start from page 2
        return formsApi.submissions.list(props.form.id, { query: { page } })
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
