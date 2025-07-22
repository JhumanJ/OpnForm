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
        v-model="search"
      />
      <USelectMenu
        size="sm"
        variant="ghost"
        class="w-24"
        v-if="hasStatus"
        v-model="selectedStatus"
        value-key="value"
        :items="statusList"
        :search-input="false"
      />

      <TableColumnManager 
        class="ml-auto"
        :table-state="tableState"
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
      v-if="form"
      ref="table"
      :columns="allColumns"
      :column-visibility="columnVisibility"
      :column-pinning="columnPinning"
      :column-sizing="columnSizing"
      :data="filteredTableData"
      :loading="loading"
      sticky
      class="flex-1"
      :style="{ maxHeight, ...columnSizeVars }"
      :ui="{
        thead: 'bg-neutral-50',
        th: 'p-1 relative overflow-hidden bg-neutral-50',
        td: 'px-3 py-2 overflow-hidden data-[pinned=left]:bg-white data-[pinned=left]:border-t data-[pinned=left]:border-r data-[pinned=left]:border-neutral-200 border-r',
      }"
    >
      <template v-for="col in tableColumns" :key="`${col.id}-header`" #[`${col.id}-header`]="{ column }">
        <TableHeader 
          :column="column"
          :table-state="tableState"
          :is-wrapped="columnWrapping[col.id]"
          @resize="handleColumnResize"
        />
      </template>
    
      <template 
        v-for="col in tableColumns" 
        :key="col.id"
        #[`${col.id}-cell`]="{ row }"
      >
        <div 
          :class="getCellClasses(col.id)"
          :style="{ 
            width: `var(--col-${col.id}-size, auto)`, 
          }"
        >
          <component
            :is="fieldComponents[col.type]"
            class="border-neutral-100 dark:border-neutral-900"
            :property="col"
            :value="row.original[col.id]"
          />
        </div>
      </template>
      
      <template #actions-cell="{ row }">
        <div class="flex justify-center" :style="{ width: `var(--col-actions-size, auto)` }">
          <RecordOperations
            :form="form"
            :submission="row.original"
            @deleted="(submission) => $emit('deleted', submission)"
            @updated="(submission) => $emit('updated', submission)"
          />
        </div>
      </template>
    </UTable>
  </div>
</template>

<script setup>
import { formsApi } from '~/api'
import { useEventListener, refDebounced } from '@vueuse/core'
import { useTableState } from '~/composables/components/tables/useTableState'
import OpenText from "./components/OpenText.vue"
import OpenUrl from "./components/OpenUrl.vue"
import OpenSelect from "./components/OpenSelect.vue"
import OpenMatrix from "./components/OpenMatrix.vue"
import OpenDate from "./components/OpenDate.vue"
import OpenFile from "./components/OpenFile.vue"
import OpenCheckbox from "./components/OpenCheckbox.vue"
import OpenPayment from "./components/OpenPayment.vue"
import OpenSubmissionStatus from "./components/OpenSubmissionStatus.vue"
import RecordOperations from "../components/RecordOperations.vue"
import TableHeader from "./components/TableHeader.vue"
import TableColumnManager from "./components/TableColumnManager.vue"
import Fuse from "fuse.js"

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

// Get workspace for table state
const { current: workspace } = useCurrentWorkspace()

// Initialize table state (includes preferences internally)
const tableState = useTableState(
  computed(() => props.form),
  workspace && !workspace.is_readonly
)

const { tableColumns: allColumns, columnVisibility, columnPinning, columnSizing, columnWrapping, handleColumnResize: handleColumnResizeState } = tableState

const tableColumns = computed(() => {
  return allColumns.value.filter(column => column.id !== 'actions')
})

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
  status: OpenSubmissionStatus,
}

const exportLoading = ref(false)
const table = ref(null)
const root = ref(null)
const topBar = ref(null)
const isExpanded = ref(false)
const maxHeight = ref('800px') // fallback default
const search = ref("")
const debouncedSearch = refDebounced(search, 300)
const selectedStatus = ref('all')

const statusList = [
  { label: 'All', value: 'all' },
  { label: 'Submitted', value: 'completed' },
  { label: 'In Progress', value: 'partial' }
]

const filteredTableData = computed(() => {
  let data = [...props.data]

  // Status filter (client-side)
  if (hasStatus.value && selectedStatus.value !== 'all') {
    data = data.filter(row => {
      if (selectedStatus.value === 'completed') return row.status !== 'partial'
      if (selectedStatus.value === 'partial') return row.status === 'partial'
      return true
    })
  }

  // Search (client-side, fuzzy)
  if (debouncedSearch.value && debouncedSearch.value.trim() !== "") {
    const fuse = new Fuse(data, {
      keys: allColumns.value.map(col => col.id).filter(id => id !== 'actions'),
      threshold: 0.4,
    })
    return fuse.search(debouncedSearch.value).map(res => res.item)
  } else {
    // Default sort by created_at desc
    return data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }
})

const hasStatus = computed(() => {
  return props.form?.is_pro && (props.form.enable_partial_submissions ?? false)
})

// Since UTable only renders when form exists, no need for safe wrappers

// Cell styling based on wrapping preferences
const getCellClasses = (columnId) => {
  const isWrapped = columnWrapping.value?.[columnId] || false
  return {
    'text-truncate whitespace-nowrap': !isWrapped,
    'whitespace-normal': isWrapped,
  }
}

// Column size CSS variables - similar to TanStack Table React example
const columnSizeVars = computed(() => {
  // Ensure reactivity to columnSizing changes
  const sizing = columnSizing.value

  if (!sizing) return {}

  const colSizes = {}

  for (const [colId, size] of Object.entries(sizing)) {
    colSizes[`--header-${colId}-size`] = `${size}px`
    colSizes[`--col-${colId}-size`] = `${size}px`
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

const handleColumnResize = (columnId, newSize) => {
  handleColumnResizeState(columnId, newSize)
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
    columns: columnVisibility.value
  }).then(data => {
    
    // Convert string to Blob if needed
    let blob
    if (typeof data === 'string') {
      blob = new Blob([data], { type: 'text/csv;charset=utf-8;' })
    } else if (data instanceof Blob) {
      blob = data
    } else {
      throw new Error('Invalid export data format')
    }
    
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