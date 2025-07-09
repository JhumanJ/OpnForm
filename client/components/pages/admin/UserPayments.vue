<template>
  <AdminCard
    v-if="props.user.stripe_id"
    title="Payments"
    icon="heroicons:currency-dollar-16-solid"
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
      <template #id-cell="{ row }">
        <a
          :href="'https://dashboard.stripe.com/invoices/' + row.original.id"
          target="_blank"
          class="text-xs select-all bg-purple-50 border-purple-200 text-purple-500 rounded-md px-2 py-1 border"
        >
          <Icon
            name="bx:bxl-stripe"
            class="h-4 w-4 inline-block"
          />
          {{ row.original.id }}
        </a>
      </template>
      <template #amount_paid-cell="{ row }">
        <span class="font-semibold">${{ parseFloat(row.original.amount_paid / 100).toFixed(2) }}</span>
      </template>
      <template #status-cell="{ row }">
        <span
          class="text-xs select-all rounded-md px-2 py-1 border"
          :class="row.original.status == 'paid' ? 'bg-green-50 border-green-200 text-green-500' : 'bg-yellow-50 border-yellow-200 text-yellow-500'"
        >
          {{ row.original.status }}
        </span>
      </template>
    </UTable>
    <div
      v-if="payments?.length > pageCount"
      class="flex justify-end px-3 py-3.5 border-t border-neutral-200 dark:border-neutral-700"
    >
      <UPagination
        v-model="page"
        :page-count="pageCount"
        :total="payments?.length"
      />
    </div>
  </AdminCard>
</template>

<script setup>
import { adminApi } from '~/api'

const props = defineProps({
  user: {type: Object, required: true}
})

const loading = ref(true)
const payments = ref([])
const page = ref(1)
const pageCount = 5

const rows = computed(() => {
  return payments.value.slice((page.value - 1) * pageCount, (page.value) * pageCount)
})
onMounted(() => {
  getPayments()
})

const getPayments = () => {
  if (!props.user.stripe_id) return
  loading.value = true
  adminApi.billing.getPayments(props.user.id).then(data => {
    loading.value = false
    payments.value = data.payments
  }).catch(error => {
    useAlert().error(error.data.message)
    loading.value = false
  })
}


const columns = [{
  accessorKey: 'id',
  header: 'ID'
}, {
  accessorKey: 'amount_paid',
  header: 'Amount paid',
  sortable: true
}, {
  accessorKey: 'name',
  header: 'Name',
  sortable: true
}, {
  accessorKey: 'status',
  header: 'Status',
  sortable: true
}, {
  accessorKey: 'creation_date',
  header: 'Creation date',
  sortable: true
}]

</script>
