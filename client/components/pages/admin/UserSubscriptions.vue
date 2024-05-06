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
      :rows="rows"
      class="-mx-6"
    >
      <template #stripe_id-data="{ row }">
        <a
          :href="'https://dashboard.stripe.com/subscriptions/' + row.stripe_id"
          target="_blank"
          class="text-xs select-all bg-purple-50 border-purple-200 text-purple-500 rounded-md px-2 py-1 border"
        >
          <Icon
            name="bx:bxl-stripe"
            class="h-4 w-4 inline-block"
          />
          {{ row.stripe_id }}
        </a>
      </template>
      <template #status-data="{ row }">
        <span
          class="text-xs select-all rounded-md px-2 py-1 border"
          :class="row.status == 'active' ? 'bg-green-50 border-green-200 text-green-500' : 'bg-yellow-50 border-yellow-200 text-yellow-500'"
        >
          {{ row.status }}
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
    opnFetch("/moderator/billing/" + props.user.id + "/subscriptions",).then(data => {
        loading.value = false
        subscriptions.value = data.subscriptions
    }).catch(error => {
        useAlert().error(error.data.message)

        loading.value = false
    })
}


const columns = [{
    key: 'id',
    label: 'ID'
}, {
    key: 'stripe_id',
    label: 'Stripe ID'
}, {
    key: 'name',
    label: 'Name',
    sortable: true
}, {
    key: 'creation_date',
    label: 'Creation date',
    sortable: true
}, {
    key: 'plan',
    label: 'Plan',
    sortable: true,
    direction: 'desc'
}, {
    key: 'status',
    label: 'Status'
}]

</script>
