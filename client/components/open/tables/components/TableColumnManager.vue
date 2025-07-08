<template>
  <div>
    <UPopover v-model:open="isPopoverOpen" arrow :content="popoverContent">
      <UButton
        size="sm"
        variant="ghost"
        label="Columns"
        color="neutral"
        trailing-icon="i-lucide-chevron-down"
        class="ml-auto"
      />

      <template #content>
        <div class="w-80 p-4 space-y-4">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <UInput
              variant="outline"
              placeholder="Search columns..."
              icon="i-heroicons-magnifying-glass"
              v-model="searchQuery"
            />
            <UButton
              variant="ghost"
              color="neutral"
              icon="i-heroicons-x-mark"
              @click="isPopoverOpen = false"
            />
          </div>

          <!-- Column Sections -->
          <template v-for="section in columnSections" :key="section.type">
            <div 
              v-if="section.columns.length > 0"
              class="space-y-2"
            >
              <div class="flex items-center justify-between">
                <h4 class="text-xs text-gray-500">{{ section.title }}</h4>
                <UButton
                  size="xs"
                  variant="link"
                  :label="section.actionLabel"
                  @click="toggleAllColumns(!section.visible)"
                />
              </div>
              <div class="space-y-1 max-h-32 overflow-y-auto">
                <draggable
                  :list="section.columns"
                  item-key="id"
                  class="space-y-1"
                  :ghost-class="['opacity-50', 'bg-blue-50', 'rounded-md']"
                  :chosen-class="['bg-blue-100', 'rounded-md']"
                  :animation="200"
                  group="columns"
                  @end="handleDragEnd"
                >
                  <template #item="{ element: column }">
                    <div class="group">
                      <div class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-50 transition-colors">
                        <!-- Drag Handle -->
                        <div class="w-4 h-4 flex items-center justify-center opacity-60 group-hover:opacity-100 transition-opacity">
                          <UIcon name="clarity:drag-handle-line" class="h-6 w-6 -ml-1 shrink-0 text-gray-500" />
                        </div>

                        <!-- Column Icon -->
                        <div class="w-4 h-4 flex items-center justify-center">
                          <BlockTypeIcon 
                            v-if="column.type"
                            :type="column.type"
                            bg-class="bg-transparent"
                            text-class="text-gray-500"
                            class="flex-shrink-0"
                          />
                        </div>

                        <!-- Column Name with Tooltip -->
                        <UTooltip :text="column.header || column.id" :content="{ align: 'start' }">
                          <span class="flex-1 text-sm truncate">
                            {{ column.header || column.id }}
                          </span>
                        </UTooltip>

                        <!-- Removed Indicator -->
                        <UTooltip v-if="column.isRemoved" text="Column was removed from form" :content="{ align: 'end' }">
                          <UIcon name="i-heroicons-trash" class="w-3 h-3 text-red-500" />
                        </UTooltip>

                        <!-- Column Actions -->
                        <div class="flex items-center gap-1">
                          <!-- Pin Toggle -->
                          <UTooltip :text="getPinTooltip(column.id)">
                            <UButton
                              size="xs"
                              variant="ghost"
                              :icon="getPinIcon(column.id)"
                              :color="getColumnPreference(column.id).pinned ? 'primary' : 'gray'"
                              @click="togglePin(column.id)"
                            />
                          </UTooltip>

                          <!-- Wrap Toggle -->
                          <UTooltip :text="getColumnPreference(column.id).wrapped ? 'Disable text wrapping' : 'Enable text wrapping'">
                            <UButton
                              size="xs"
                              variant="ghost"
                              :icon="getColumnPreference(column.id).wrapped ? 'i-heroicons-arrows-pointing-out' : 'i-heroicons-arrows-pointing-in'"
                              :color="getColumnPreference(column.id).wrapped ? 'primary' : 'gray'"
                              @click="toggleWrap(column.id)"
                            />
                          </UTooltip>

                          <!-- Visibility Toggle Button -->
                          <UTooltip :text="section.visible ? 'Hide' : 'Show'">
                            <UButton
                              size="xs"
                              variant="ghost"
                              color="gray"
                              :icon="section.visible ? 'i-heroicons-eye' : 'i-heroicons-eye-slash'"
                              @click="handleVisibilityChange(column, !section.visible)"
                            />
                          </UTooltip>
                        </div>
                      </div>
                    </div>
                  </template>
                </draggable>
              </div>
            </div>
          </template>

          <!-- Footer Actions -->
          <div class="flex items-center justify-between pt-2 border-t border-gray-200">
            <UButton
              size="xs"
              variant="ghost"
              color="neutral"
              label="Reset Order"
              @click="resetColumnOrder"
            />
            <UButton
              size="xs"
              variant="ghost"
              color="neutral"
              label="Reset All"
              @click="columnPreferences.resetPreferences()"
            />
          </div>
        </div>
      </template>
    </UPopover>
  </div>
</template>

<script setup>
import draggable from 'vuedraggable'
import BlockTypeIcon from '~/components/open/forms/components/BlockTypeIcon.vue'

const props = defineProps({
  column: {
    type: Array,
    required: true,
  },
  columnVisibility: {
    type: Object,
    default: () => ({}),
  },
  columnPreferences: {
    type: Object,
    required: true,
  },
  popoverContent: {
    type: Object,
    default: () => ({
      align: 'end',
      side: 'bottom',
    }),
  },
})

const emit = defineEmits(['resize-start', 'column-visibility-change', 'column-order-change'])

const isPopoverOpen = ref(false)
const searchQuery = ref('')

// Create reactive copies of the column sections for dragging
const visibleColumns = ref([])
const hiddenColumns = ref([])

// Get column preferences helper
const getColumnPreference = (columnId) => {
  return props.columnPreferences.getColumnPreference(columnId)
}

// Filter columns based on search query and maintain order
const filteredColumns = computed(() => {
  const columns = props.column || []
  if (!Array.isArray(columns)) return []
  
  const query = searchQuery.value.toLowerCase()
  return columns.filter(column => 
    column.id !== 'actions' && 
    (column.header || column.id).toLowerCase().includes(query)
  )
})

// Get current visibility state
const getColumnVisibility = (column) => { 
  // Check if column is explicitly hidden in visibility state
  if (props.columnVisibility[column.id] === false) {
    return false
  }
  // Default to visible for regular columns, hidden for removed columns
  return props.columnVisibility[column.id] || !column.isRemoved
}

// Update the reactive column arrays when props change
const updateColumnArrays = () => {
  const visible = []
  const hidden = []
  
  filteredColumns.value.forEach(column => {
    if (getColumnVisibility(column)) {
      visible.push(column)
    } else {
      hidden.push(column)
    }
  })
  
  visibleColumns.value = visible
  hiddenColumns.value = hidden
}

// Column sections configuration
const columnSections = computed(() => [
  {
    type: 'visible',
    title: 'Shown in table',
    actionLabel: 'Hide All',
    visible: true,
    columns: visibleColumns.value
  },
  {
    type: 'hidden',
    title: 'Hidden in table',
    actionLabel: 'Show All',
    visible: false,
    columns: hiddenColumns.value
  }
])

// Watch for changes in filtered columns and update arrays
watch(filteredColumns, updateColumnArrays, { immediate: true })

// Watch for changes in column visibility and update arrays
watch(() => props.columnVisibility, updateColumnArrays, { deep: true })

// Handle drag end event
const handleDragEnd = () => {
  // Rebuild the complete column order
  const newOrder = []
  
  // Add all visible columns first
  visibleColumns.value.forEach(col => {
    newOrder.push(col.id)
  })
  
  // Add all hidden columns
  hiddenColumns.value.forEach(col => {
    newOrder.push(col.id)
  })
  
  // Add any remaining columns that weren't in either list
  props.column.forEach(col => {
    if (!newOrder.includes(col.id)) {
      newOrder.push(col.id)
    }
  })
  
  emit('column-order-change', newOrder)
}

// Toggle pin state
const togglePin = (columnId) => {
  const pref = getColumnPreference(columnId)
  let newPinState = false

  if (!pref.pinned) {
    newPinState = 'left'
  } else if (pref.pinned === 'left') {
    newPinState = 'right'
  }

  props.columnPreferences.setColumnPreference(columnId, { pinned: newPinState })
}

// Toggle wrap state
const toggleWrap = (columnId) => {
  const pref = getColumnPreference(columnId)
  props.columnPreferences.setColumnPreference(columnId, { wrapped: !pref.wrapped })
}

// Get pin icon
const getPinIcon = (columnId) => {
  const pref = getColumnPreference(columnId)
  if (pref.pinned === 'left') return 'i-heroicons-arrow-left-on-rectangle'
  if (pref.pinned === 'right') return 'i-heroicons-arrow-right-on-rectangle'
  return 'i-heroicons-rectangle-stack'
}

// Get pin tooltip
const getPinTooltip = (columnId) => {
  const pref = getColumnPreference(columnId)
  if (pref.pinned === 'left') return 'Pinned to left - click to pin right'
  if (pref.pinned === 'right') return 'Pinned to right - click to unpin'
  return 'Not pinned - click to pin left'
}

// Reset column order to original
const resetColumnOrder = () => {
  const columns = props.column || []
  const originalOrder = columns
    .filter(col => col.id !== 'actions')
    .map(col => col.id)
  emit('column-order-change', originalOrder)
}

// Handle visibility change
const handleVisibilityChange = (column, visible) => {
  emit('column-visibility-change', [{ columnId: column.id, visible }])
}

// Toggle all columns
const toggleAllColumns = (visible = true) => {
  const columns = props.column || []
  emit('column-visibility-change', columns
    .filter(column => column.id !== 'actions')
    .map(column => ({ columnId: column.id, visible: visible })))
}
</script>

<style scoped>
/* Ensure drag cursor is applied to the entire row */
.group {
  cursor: grab;
}

.group:active {
  cursor: grabbing;
}
</style>
