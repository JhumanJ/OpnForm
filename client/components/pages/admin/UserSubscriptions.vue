<template>
  <AdminCard
    v-if="props.user.stripe_id"
    title="Subscriptions"
    icon="heroicons:credit-card-16-solid"
  >
    <UTable
      :loading="loading"
      :loading-state="{ icon: 'i-heroicons-arrow-path-20-solid', label: 'Loading...' }"
      :progress="{ color: 'primary', animation: 'carousel' }"
      :empty-state="{ icon: 'i-heroicons-circle-stack-20-solid', label: 'No items.' }"
      :columns="columns"
      :data="rows"
      class="-mx-6"
    >
      <template #stripe_id-cell="{ row }">
        <a
          :href="'https://dashboard.stripe.com/subscriptions/' + row.original.stripe_id"
          target="_blank"
          class="text-xs select-all bg-purple-50 border-purple-200 text-purple-500 rounded-md px-2 py-1 border"
        >
          <Icon
            name="bx:bxl-stripe"
            class="h-4 w-4 inline-block"
          />
          {{ row.original.stripe_id }}
        </a>
      </template>
      <template #status-cell="{ row }">
        <span
          class="text-xs select-all rounded-md px-2 py-1 border"
          :class="row.original.status == 'active' ? 'bg-green-50 border-green-200 text-green-500' : 'bg-yellow-50 border-yellow-200 text-yellow-500'"
        >
          {{ row.original.status }}
        </span>
      </template>
    </UTable>
    <div
      v-if="subscriptions?.length > pageCount"
      class="flex justify-end px-3 py-3.5 border-t border-gray-200 dark:border-gray-700"
    >
      <UPagination
        v-model="page"
        :page-count="pageCount"
        :total="subscriptions.length"
      />
    </div>
  </AdminCard>
</template>

<script setup>
import { adminApi } from '~/api'

const props = defineProps({
    user: { type: Object, required: true }
})

const loading = ref(true)
const subscriptions = ref([])
const page = ref(1)
const pageCount = 5

const rows = computed(() => {
  return subscriptions.value.slice((page.value - 1) * pageCount, (page.value) * pageCount)
})
onMounted(() => {
    getSubscriptions()
})

const getSubscriptions = () => {
    if (!props.user.stripe_id) return
    loading.value = true
    adminApi.billing.getSubscriptions(props.user.id).then(data => {
        loading.value = false
        subscriptions.value = data.subscriptions
    }).catch(error => {
        useAlert().error(error.data.message)

        loading.value = false
    })
}


const columns = [{
    accessorKey: 'id',
    header: 'ID'
}, {
    accessorKey: 'stripe_id',
    header: 'Stripe ID'
}, {
    accessorKey: 'name',
    header: 'Name',
    sortable: true
}, {
    accessorKey: 'creation_date',
    header: 'Creation date',
    sortable: true
}, {
    accessorKey: 'plan',
    header: 'Plan',
    sortable: true,
    direction: 'desc'
}, {
    accessorKey: 'status',
    header: 'Status'
}]

</script>
