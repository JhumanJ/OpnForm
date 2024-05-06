<template>
    <AdminCard
      title="Workspaces"
      icon="heroicons:globe-alt"
    >
      <UTable
        :loading-state="{ icon: 'i-heroicons-arrow-path-20-solid', label: 'Loading...' }"
        :progress="{ color: 'primary', animation: 'carousel' }"
        :empty-state="{ icon: 'i-heroicons-circle-stack-20-solid', label: 'No items.' }"
        :columns="columns"
        :rows="rows"
        class="-mx-6"
      >
      <template #plan-data="{ row }">
        <span
          class="text-xs select-all rounded-md px-2 py-1 border"
          :class="userPlanStyles(row.plan)"
        >
          {{ row.plan }}
        </span>
      </template>
      </UTable>
      <div 
        v-if="workspaces?.length > pageCount"
        class="flex justify-end px-3 py-3.5 border-t border-gray-200 dark:border-gray-700">
        <UPagination
          v-model="page"
          :page-count="pageCount"
          :total="workspaces?.length"
        />
      </div>
    </AdminCard>
  </template>
  
<script setup>

const props = defineProps({
    user: { type: Object, required: true }
})

const workspaces = ref([])
const page = ref(1)
const pageCount = 2

const rows = computed(() => {
    return props.user.workspaces.slice((page.value - 1) * pageCount, (page.value) * pageCount)
})


const columns = [{
    key: 'id',
    label: 'ID'
}, {
    key: 'name',
    label: 'Name',
    sortable: true
}, {
    key: 'plan',
    label: 'Plan',
    sortable: true
}, {
    key: 'forms_count',
    label: '# of forms',
    sortable: true
}]

function userPlanStyles(plan) {
    switch (plan) {
        case 'pro':
            return 'capitalize text-xs select-all bg-green-50 rounded-md px-2 py-1 border border-green-200 text-green-500'
        case 'enterprise':
            return 'capitalize text-xs select-all bg-blue-50 rounded-md px-2 py-1 border border-blue-200  text-blue-500'
        default:
            return 'capitalize text-xs select-all bg-gray-50 rounded-md px-2 py-1 border'
    }
}

</script>
  