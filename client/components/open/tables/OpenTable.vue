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

      <TableColumnManager 
        class="ml-auto"
        :column="safeTableColumns"
        :column-visibility="tableState.columnVisibility"
        :column-preferences="columnPreferences"
        @resize-start="handleResizeStart"
        @column-visibility-change="handleColumnVisibilityChange"
        @column-order-change="handleColumnOrderChange"
      />

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
      :columns="safeTableColumns"
      v-model:column-visibility="tableState.columnVisibility"
      v-model:column-pinning="tableState.columnPinning"
      v-model:column-sizing="tableState.columnSizing"
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
      <template v-for="col in safeTableColumns.filter(column => !['actions', 'status'].includes(column.id))" :key="`${col.id}-header`" #[`${col.id}-header`]="{ column }">
        <TableHeader 
          :column="column"
          :column-preferences="columnPreferences"
          :is-wrapped="tableState.columnWrapping[col.id]"
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
        v-for="col in safeTableColumns.filter(column => !['actions', 'status'].includes(column.id))" 
        :key="col.id"
        #[`${col.id}-cell`]="{ row }"
      >
        <div 
          :class="getCellClasses(col.id)"
          :style="getCellStyles(col.id)"
        >
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
            :structure="safeTableColumns"
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
import { formsApi } from '~/api'
import { useEventListener } from '@vueuse/core'
import { useTableColumnPreferences } from '~/composables/useTableColumnPreferences'
import { useTableState } from '~/composables/useTableState'
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
import TableColumnManager from "./components/TableColumnManager.vue"

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

// Initialize column preferences system
const columnPreferences = useTableColumnPreferences(
  computed(() => props.form?.id || props.form?.slug)
)

// Get workspace for table state
const { current: workspace } = useCurrentWorkspace()

const tableState = useTableState(
  computed(() => props.form),
  columnPreferences,
  workspace
)


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





const hasStatus = computed(() => {
  return props.form?.is_pro && (props.form.enable_partial_submissions ?? false)
})

const tableData = computed(() => {
  return [...props.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// Ensure tableColumns is always an array
const safeTableColumns = computed(() => {
  const columns = tableState.tableColumns?.value
  if (!columns) return []
  if (!Array.isArray(columns)) return []
  return columns
})



// Cell styling based on wrapping preferences
const getCellClasses = (columnId) => {
  const isWrapped = tableState.columnWrapping.value?.[columnId] || false
  return {
    'text-truncate': !isWrapped,
    'whitespace-normal': isWrapped,
    'whitespace-nowrap': !isWrapped
  }
}

const getCellStyles = (columnId) => {
  const isWrapped = tableState.columnWrapping.value?.[columnId] || false
  return {
    width: `var(--col-${columnId}-size, auto)`,
    maxWidth: isWrapped ? 'none' : '300px'
  }
}

// Column size CSS variables - similar to TanStack Table React example
const columnSizeVars = computed(() => {
  // Add dependency on columnSizing to trigger reactivity
  tableState.columnSizing.value
  
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

const handleColumnVisibilityChange = (changes) => {
  // Update all visibility states - the v-model:column-visibility will handle the table updates
  changes.forEach(({ columnId, visible }) => {
    columnPreferences.setColumnPreference(columnId, { visible })
  })
}

const handleColumnOrderChange = (newOrder) => {
  // Update column order in preferences
  columnPreferences.setColumnOrder(newOrder)
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