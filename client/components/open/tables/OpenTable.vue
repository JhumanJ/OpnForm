<template>
  <div 
    ref="root" 
    :class="[
      'flex-1 divide-y divide-accented w-full flex flex-col', 
      { 'fixed inset-0 z-50 bg-white dark:bg-neutral-900 z-[70]': isExpanded }
    ]"
  >
    <div ref="topBar" class="flex items-center gap-2 p-2 overflow-x-auto">
      <UInput 
        class="max-w-sm min-w-[12ch]" 
        placeholder="Search..." 
        @update:model-value="table?.tableApi?.setGlobalFilter($event)"
      />
      <USelectMenu
        class="w-32"
        v-if="hasStatus"
        :model-value="selectedStatus"
        :items="statusList"
        :search-input="false"
        @update:model-value="handleStatusFilter"
      />

      <UDropdownMenu
        :items="dropdownItems"
        :content="{ align: 'end' }"
      >
        <UButton
          label="Columns"
          color="neutral"
          variant="outline"
          trailing-icon="i-lucide-chevron-down"
          class="ml-auto"
        />
        <template #removed-fields>
          <div class="flex items-center gap-2 w-full">
            <hr class="border-neutral-200 grow" />
            <p class="text-sm text-neutral-500">Removed Fields</p>
            <hr class="border-neutral-200 grow" />
          </div>
        </template>
      </UDropdownMenu>

      <UButton
        color="neutral"
        variant="outline"
        icon="heroicons:arrow-down-tray"
        label="Export"
        :loading="exportLoading"
        @click="downloadAsCsv"
      />
      <UButton
        color="neutral"
        variant="outline"
        :icon="isExpanded ? 'i-heroicons-arrows-pointing-in' : 'i-heroicons-arrows-pointing-out'"
        @click="toggleExpanded"
      />
    </div>

    <UTable
      ref="table"
      :columns="tableColumns"
      v-model:column-visibility="columnVisibility"
      v-model:column-pinning="columnPinning"
      :data="tableData"
      :loading="loading"
      sticky
      class="flex-1"
      :style="{ maxHeight }"
    >
      <template 
        v-for="col in tableColumns.filter(col => !['actions', 'status'].includes(col.id))" 
        :key="col.id"
        #[`${col.id}-cell`]="{ row }"
      >
        <component
          :is="fieldComponents[col.type]"
          class="border-gray-100 dark:border-gray-900"
          :property="col"
          :value="row.original[col.id]"
        />
      </template>
      
      <template #actions-cell="{ row }">
        <div class="flex justify-center">
          <RecordOperations
            :form="form"
            :structure="columns"
            :submission="row.original"
            @deleted="(submission) => $emit('deleted', submission)"
            @updated="(submission) => $emit('updated', submission)"
          />
        </div>
      </template>
      
      <template #status-cell="{ row }">
        <UBadge
          :label="row.original.status === 'partial' ? 'In Progress' : 'Submitted'"
          :color="row.original.status === 'partial' ? 'warning' : 'success'"
          variant="subtle"
        />
      </template>
    </UTable>
  </div>
</template>

<script setup>
import clonedeep from 'clone-deep'
import { useEventListener } from '@vueuse/core'
import OpenText from "./components/OpenText.vue"
import OpenUrl from "./components/OpenUrl.vue"
import OpenSelect from "./components/OpenSelect.vue"
import OpenMatrix from "./components/OpenMatrix.vue"
import OpenDate from "./components/OpenDate.vue"
import OpenFile from "./components/OpenFile.vue"
import OpenCheckbox from "./components/OpenCheckbox.vue"
import OpenPayment from "./components/OpenPayment.vue"
import RecordOperations from "../components/RecordOperations.vue"

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  }
})

defineEmits(["updated", "deleted"])

const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()
const form = storeToRefs(workingFormStore).content
const workspace = computed(() => workspacesStore.getCurrent)
const runtimeConfig = useRuntimeConfig()

const fieldComponents = {
  text: OpenText,
  rich_text: OpenText,
  number: OpenText,
  rating: OpenText,
  scale: OpenText,
  slider: OpenText,
  select: OpenSelect,
  matrix: OpenMatrix,
  multi_select: OpenSelect,
  date: OpenDate,
  files: OpenFile,
  checkbox: OpenCheckbox,
  url: OpenUrl,
  email: OpenText,
  phone_number: OpenText,
  signature: OpenFile,
  payment: OpenPayment,
  barcode: OpenText,
}

const exportLoading = ref(false)
const table = ref(null)
const root = ref(null)
const topBar = ref(null)
const isExpanded = ref(false)
const maxHeight = ref('800px') // fallback default
const selectedStatus = ref('All')

const statusList = [
  { label: 'All', value: 'all' },
  { label: 'Submitted', value: 'completed' },
  { label: 'In Progress', value: 'partial' }
]

const handleStatusFilter = (selected) => {
  selectedStatus.value = selected.label
  if (table.value?.tableApi) {
    const statusColumn = table.value.tableApi.getColumn('status')
    if (statusColumn) {
      if (selected.value === 'all') {
        statusColumn.setFilterValue(undefined)
      } else {
        statusColumn.setFilterValue(selected.value)
      }
    }
  }
}

const dropdownItems = computed(() => {
  if (!table.value?.tableApi) return []

  const allColumns = table.value.tableApi.getAllColumns()
  const items = allColumns.filter((column) => !column.columnDef.isRemoved)
  const removeditems = allColumns.filter((column) => column.columnDef.isRemoved)

  if (removeditems.length > 0) {
    items.push({
      label: 'Removed Fields',
      type: 'label',
      slot: 'removed-fields',
    })

    items.push(...removeditems)
  }
    
  return items.map((column) => {
    if(column.type === 'label') {
      return column
    }
    return {
      label: column.columnDef.header,
      type: 'checkbox',
      checked: column.getIsVisible(),
      onUpdateChecked(checked) {
        table.value?.tableApi?.getColumn(column.id)?.toggleVisibility(!!checked)
      },
      onSelect(e) {
        e?.preventDefault()
      }
    }
  })
})

const hasActions = computed(() => {
  return !workspace.value.is_readonly
})

const hasStatus = computed(() => {
  return form.value.is_pro && (form.value.enable_partial_submissions ?? false)
})

const tableData = computed(() => {
  return [...props.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// Table columns
const tableColumns = computed(() => {
  const properties = clonedeep(form.value.properties).filter((field) => {
    return !['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'].includes(field.type)
  })

  const cols = properties.map(col => ({
    ...col,
    accessorKey: col.id,
    header: col.name,
  }))

  if (form.value?.removed_properties) {
    form.value.removed_properties.forEach(property => {
      cols.push({
        ...property,
        accessorKey: property.id,
        header: property.name,
        isRemoved: true
      })
    })
  }

  // Add created_at column if not present
  if (!properties.find(property => property.id === 'created_at')) {
    cols.push({
      id: 'created_at',
      accessorKey: 'created_at',
      header: 'Created at',
      type: 'date'
    })
  }
  
  if (hasStatus.value) {
    cols.push({
      id: 'status',
      accessorKey: 'status',
      header: 'Status',
      enableColumnFilter: true,
      filterFn: 'equals'
    })
  }
  
  if (hasActions.value) {
    cols.push({
      id: 'actions',
      accessorKey: 'actions',
      header: 'Actions'
    })
  }
  
  return cols
})

// Column pinning state
const columnPinning = ref({
  left: [],
  right: ['actions']
})

// Column visibility state
const columnVisibility = ref({})

const computeMaxHeight = () => {
  if (!root.value || !topBar.value) return
  
  const topBarHeight = topBar.value.offsetHeight
  
  if (isExpanded.value) {
    maxHeight.value = `${window.innerHeight - topBarHeight}px`
  } else {
    const rootRect = root.value.getBoundingClientRect()
    // 16px bottom margin for breathing room
    maxHeight.value = `${window.innerHeight - rootRect.top - topBarHeight - 16}px`
  }
}

const toggleExpanded = () => {
  isExpanded.value = !isExpanded.value
  nextTick(() => {
    computeMaxHeight()
  })
}

onMounted(() => {
  computeMaxHeight()
})

useEventListener(window, 'resize', computeMaxHeight)

// Download as CSV
const downloadAsCsv = () => {
  if (exportLoading.value) {
    return
  }

  exportLoading.value = true
  const exportUrl = runtimeConfig.public.apiBase + '/open/forms/' + form.value.id + '/submissions/export'
  opnFetch(exportUrl, {
    responseType: "blob",
    method: "POST",
    body: {
      columns: [] // TODO: Add columns to export
    }
  }).then(blob => {
    const filename = `${form.value.slug}-${Date.now()}-submissions.csv`
    const a = document.createElement("a")
    document.body.appendChild(a)
    a.style = "display: none"
    const url = window.URL.createObjectURL(blob)
    a.href = url
    a.download = filename
    a.click()
    window.URL.revokeObjectURL(url)
  }).catch((error) => {
    console.error(error)
  }).finally(() => {
    exportLoading.value = false
  })
}
</script>