<template>
  <AdminCard
    title="Deleted forms"
    icon="heroicons:trash-16-solid"
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
      <template #actions-data="{ row }">
        <VButton
          :loading="restoringForm"
          native-type="button"
          size="small"
          color="white"
          @click.prevent="restoreForm(row.slug)"
        >
          Restore
        </VButton>
      </template>
    </UTable>
    <div 
      v-if="forms?.length > pageCount"
      class="flex justify-end px-3 py-3.5 border-t border-gray-200 dark:border-gray-700">
      <UPagination
        v-model="page"
        :page-count="pageCount"
        :total="forms.length"
      />
    </div>
  </AdminCard>
</template>

<script setup>

const props = defineProps({
    user: { type: Object, required: true }
})

const loading = ref(true)
const restoringForm = ref(false)
const forms = ref([])
const page = ref(1)
const pageCount = 5

const rows = computed(() => {
    return forms.value.slice((page.value - 1) * pageCount, (page.value) * pageCount)
})
onMounted(() => {
    getDeletedForms()
})

const getDeletedForms = () => {
    loading.value = true
    opnFetch("/moderator/forms/" + props.user.id + "/deleted-forms",).then(data => {
        loading.value = false
        forms.value = data.forms
    }).catch(error => {
        useAlert().error(error.message)
        loading.value = false
    })
}

const restoreForm = (slug) => {
    return useAlert().confirm(
        "Are you sure you want to restore this form?",
        () => {
            restoringForm.value = true
            opnFetch("/moderator/forms/" + slug + "/restore", {
                method: 'PATCH',
            }).then(data => {
                restoringForm.value = false
                useAlert().success(data.message)
                getDeletedForms()
            }).catch(error => {
                restoringForm.value = false
                useAlert().error(error.data.message)
            })
        })
}


const columns = [{
    key: 'id',
    label: 'ID'
}, {
    key: 'slug',
    label: 'Slug',
    sortable: true
}, {
    key: 'title',
    label: 'Title',
    sortable: true
}, {
    key: 'created_by',
    label: 'Created by',
    sortable: true
}, {
    key: 'deleted_at',
    label: 'Deleted at',
    sortable: true,
}, {
    key: 'actions',
    label: 'Restore',
    sortable: false,
}]

</script>
