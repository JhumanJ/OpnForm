<template>
  <div 
    ref="root" 
    :class="[
      'flex-1 divide-y divide-accented w-full flex flex-col', 
      { 'fixed inset-0 z-50 bg-white dark:bg-neutral-900 z-[70]': isExpanded,
        'border-t mt-4': !isExpanded
       }
    ]"
  >
    <div ref="topBar" class="flex items-center gap-2 p-2 overflow-x-auto">
      <UInput 
        size="sm"
        variant="ghost"
        class="max-w-sm min-w-[12ch]" 
        placeholder="Search..." 
        icon="i-heroicons-magnifying-glass-solid"
        @update:model-value="table?.tableApi?.setGlobalFilter($event)"
      />
      <USelectMenu
        size="sm"
        variant="ghost"
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
          size="sm"
          variant="ghost"
          label="Columns"
          color="neutral"
          trailing-icon="i-lucide-chevron-down"
          class="ml-auto"
        />
        <template #removed-fields>
          <div class="flex items-center gap-2 w-full">
            <hr class="border-neutral-200 grow" />
            <p class="text-xs text-neutral-500">Removed Fields</p>
            <hr class="border-neutral-200 grow" />
          </div>
        </template>
      </UDropdownMenu>

      <UButton
        size="sm"
        color="neutral"
        variant="ghost"
        label="Export"
        :loading="exportLoading"
        @click="downloadAsCsv"
      />
      <UButton  
        size="sm"
        color="neutral"
        variant="ghost"
        :icon="isExpanded ? 'i-heroicons-arrows-pointing-in' : 'i-heroicons-arrows-pointing-out'"
        @click="toggleExpanded"
      />
    </div>

    <UTable
      ref="table"
      :columns="tableColumns"
      v-model:column-visibility="columnVisibility"
      v-model:column-pinning="columnPinning"
      v-model:column-sizing="columnSizing"
      :data="tableData"
      :loading="loading"
      sticky
      class="flex-1"
      :style="{ maxHeight, ...columnSizeVars }"
      :ui="{
        thead: 'bg-neutral-50',
        th: 'p-1',
        td: 'px-3 py-2'
      }"
      :columnSizingOptions="{
        enableColumnResizing: true,
        columnResizeMode: 'onChange',
      }"
      :default-column="{
        minSize: 60,
        maxSize: 800,
      }"

    >
      <template v-for="col in tableColumns.filter(column => !['actions', 'status'].includes(column.id))" :key="`${col.id}-header`" #[`${col.id}-header`]="{ column }">
        <TableHeader 
          :column="column"
          @resize-start="handleResizeStart"
        />
      </template>
      
      <template #actions-header="{ column }">
        <div 
          class="flex items-center justify-between group relative"
          :style="{ width: `var(--header-${column.id}-size, auto)` }"
        >
          <span class="truncate">{{ column.columnDef.header }}</span>
        </div>
      </template>
      
      <template #status-header="{ column }">
        <TableHeader 
          :column="column"
          @resize-start="handleResizeStart"
        />
      </template>
      <template 
        v-for="col in tableColumns.filter(column => !['actions', 'status'].includes(column.id))" 
        :key="col.id"
        #[`${col.id}-cell`]="{ row }"
      >
        <div :style="{ width: `var(--col-${col.id}-size, auto)` }">
          <component
            :is="fieldComponents[col.type]"
            class="border-gray-100 dark:border-gray-900"
            :property="col"
            :value="row.original[col.id]"
          />
        </div>
      </template>
      
      <template #actions-cell="{ row }">
        <div class="flex justify-center" :style="{ width: `var(--col-actions-size, auto)` }">
          <RecordOperations
            :form="form"
            :structure="tableColumns"
            :submission="row.original"
            @deleted="(submission) => $emit('deleted', submission)"
            @updated="(submission) => $emit('updated', submission)"
          />
        </div>
      </template>
      
      <template #status-cell="{ row }">
        <div :style="{ width: `var(--col-status-size, auto)` }">
          <UBadge
            :label="row.original.status === 'partial' ? 'In Progress' : 'Submitted'"
            :color="row.original.status === 'partial' ? 'warning' : 'success'"
            variant="subtle"
          />
        </div>
      </template>
    </UTable>
  </div>
</template>

<style scoped>
/* Column resizing styles */
:deep(th), :deep(td) {
  box-sizing: border-box;
  overflow: hidden;
}
</style>

<script setup>
import clonedeep from 'clone-deep'
import { formsApi } from '~/api'
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
import TableHeader from "./components/TableHeader.vue"

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  form: {
    type: Object,
    default: () => null,
  },
})

defineEmits(["updated", "deleted"])
const { current: workspace } = useCurrentWorkspace()


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
  const items = allColumns.filter((column) => !column.columnDef.isRemoved && column.id !== 'actions')
  const removeditems = allColumns.filter((column) => column.columnDef.isRemoved && column.id !== 'actions')

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
  return !workspace.value?.is_readonly
})

const hasStatus = computed(() => {
  return props.form?.is_pro && (props.form.enable_partial_submissions ?? false)
})

const tableData = computed(() => {
  return [...props.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// Table columns
const tableColumns = computed(() => {
  if (!props.form || !props.form.properties) {
    return []
  }
  
  const properties = clonedeep(props.form.properties).filter((field) => {
    return !['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'].includes(field.type)
  })

  const cols = properties.map(col => {
    const { columns: matrixColumns, ...rest } = col
    return {
      ...rest,
      ...(col.type === 'matrix' && { matrix_columns: matrixColumns }),
      id: col.id,
      accessorKey: col.id,
      header: col.name,
      enableResizing: true,
      minSize: 100,
      maxSize: 500,
    }
  })

  if (props.form?.removed_properties) {
    props.form.removed_properties.forEach(property => {
      cols.push({
        ...(property.type === 'matrix'
          ? (() => {
              const { columns: matrixColumns, ...rest } = property
              return { ...rest, matrix_columns: matrixColumns }
            })()
          : { ...property }),
        id: property.id,
        accessorKey: property.id,
        header: property.name,
        isRemoved: true,
        enableResizing: true,
        minSize: 100,
        maxSize: 500,
      })
    })
  }

  // Add created_at column if not present
  if (!properties.find(property => property.id === 'created_at')) {
    cols.push({
      id: 'created_at',
      accessorKey: 'created_at',
      header: 'Created at',
      type: 'date',
      enableResizing: true
    })
  }
  
  if (hasStatus.value) {
    cols.push({
      id: 'status',
      accessorKey: 'status',
      header: 'Status',
      enableColumnFilter: true,
      filterFn: 'equals',
      enableResizing: true,
    })
  }
  
  if (hasActions.value) {
    cols.push({
      id: 'actions',
      accessorKey: 'actions',
      header: '',
      enableResizing: false,
      size: 80,
      meta: {
        class: {
          th: 'bg-transparent',
          td: 'backdrop-blur-xs bg-white/70'
        }
      }
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

// Column sizing state
const columnSizing = ref({})

// Column size CSS variables - similar to TanStack Table React example
const columnSizeVars = computed(() => {
  // Add dependency on columnSizing to trigger reactivity
  columnSizing.value
  
  if (!table.value?.tableApi) return {}
  
  const headers = table.value.tableApi.getFlatHeaders()
  const colSizes = {}
  
  for (let i = 0; i < headers.length; i++) {
    const header = headers[i]
    if (header && header.column) {
      colSizes[`--header-${header.id}-size`] = `${header.getSize()}px`
      colSizes[`--col-${header.column.id}-size`] = `${header.column.getSize()}px`
    }
  }
  
  return colSizes
})

const computeMaxHeight = () => {
  if (!root.value || !topBar.value) return
  
  const topBarHeight = topBar.value.offsetHeight
  
  if (isExpanded.value) {
    maxHeight.value = `${window.innerHeight - topBarHeight}px`
  } else {
    const rootRect = root.value.getBoundingClientRect()
    // 16px bottom margin for breathing room
    maxHeight.value = `${window.innerHeight - rootRect.top - topBarHeight}px`
  }
}

const toggleExpanded = () => {
  isExpanded.value = !isExpanded.value
  nextTick(() => {
    computeMaxHeight()
  })
}

defineShortcuts({
  escape: () => {
    if (isExpanded.value) {
      toggleExpanded()
    }
  }
})

const handleResizeStart = (column, event) => {
  // Find the header with resize handler
  if (table.value?.tableApi) {
    const header = table.value.tableApi.getFlatHeaders().find(h => h.column.id === column.id)
    if (header && typeof header.getResizeHandler === 'function') {
      const resizeHandler = header.getResizeHandler()
      resizeHandler(event)
    }
  }
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
  formsApi.submissions.export(props.form.id, {
    columns: [] // TODO: Add columns to export
  }).then(blob => {
    const filename = `${props.form.slug}-${Date.now()}-submissions.csv`
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