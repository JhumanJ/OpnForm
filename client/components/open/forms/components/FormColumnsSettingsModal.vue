<template>
  <modal
    compact-header
    :show="show"
    v-bind="$attrs"
    @close="$emit('close')"
  >
    <template #icon>
      <Icon
        name="heroicons:adjustments-horizontal"
        class="w-8 h-8"
      />
    </template>
    <template #title>
      Manage Columns
    </template>

    <div class="px-4">
      <template
        v-for="(section, sectionIndex) in sections"
        :key="sectionIndex"
      >
        <template v-if="section.fields.length > 0">
          <h4
            class="font-semibold mb-2"
            :class="{ 'mt-4': sectionIndex > 0 }"
          >
            <div class="flex items-center justify-between">
              <span>{{ section.title }}</span>
              <div class="flex items-center gap-2 text-xs text-gray-500">
                <button
                  class="hover:text-gray-700"
                  @click="toggleAllColumns(section.fields, true)"
                >
                  Show all
                </button>
                <span class="text-gray-300">|</span>
                <button
                  class="hover:text-gray-700"
                  @click="toggleAllColumns(section.fields, false)"
                >
                  Hide all
                </button>
              </div>
            </div>
          </h4>
          <div class="border border-gray-300">
            <div class="grid grid-cols-[1fr,auto,auto] gap-4 px-4 py-2 bg-gray-50 border-b border-gray-300">
              <div class="text-sm">
                Name
              </div>
              <div class="text-sm text-center w-20">
                Display
              </div>
              <div class="text-sm text-center w-20">
                Wrap Text
              </div>
            </div>
            <div
              v-for="(field, index) in section.fields"
              :key="field.id"
              class="grid grid-cols-[1fr,auto,auto] gap-4 px-4 py-2 items-center"
              :class="{ 'border-t border-gray-300': index !== 0 }"
            >
              <p class="truncate text-sm">
                {{ field.name }}
              </p>
              <div class="flex justify-center w-20">
                <ToggleSwitchInput
                  v-model="computedDisplayColumns[field.id]"
                  wrapper-class="my-0"
                  label=""
                  :name="`display-${field.id}`"
                  @update:model-value="onChangeDisplayColumns"
                />
              </div>
              <div class="flex justify-center w-20">
                <ToggleSwitchInput
                  v-model="computedWrapColumns[field.id]"
                  wrapper-class="my-0"
                  label=""
                  :name="`wrap-${field.id}`"
                />
              </div>
            </div>
          </div>
        </template>
      </template>
    </div>
  </modal>
</template>

<script setup>
import { useStorage } from '@vueuse/core'
import clonedeep from 'clone-deep'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  form: {
    type: Object,
    required: true
  },
  columns: {
    type: Array,
    default: () => []
  },
  displayColumns: {
    type: Object,
    default: () => ({})
  },
  wrapColumns: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'update:columns', 'update:displayColumns', 'update:wrapColumns'])

const candidatesProperties = computed(() => {
  if (!props.form?.properties) return []
  
  const properties = clonedeep(props.form.properties).filter((field) => {
    return !['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'].includes(field.type)
  })

  // Add created_at column if not present
  if (!properties.find(property => property.id === 'created_at')) {
    properties.push({
      name: 'Created at',
      id: 'created_at',
      type: 'date',
      width: 140
    })
  }

  return properties
})

const sections = computed(() => [
  {
    title: 'Form Fields',
    fields: candidatesProperties.value
  },
  {
    title: 'Removed Fields',
    fields: props.form?.removed_properties || []
  }
])

// Column preferences storage
const storageKey = computed(() => `column-preferences-formid-${props.form.id}`)

const columnPreferences = useStorage(
  storageKey.value,
  {
    display: {},
    wrap: {},
    widths: {}
  },
  localStorage,
  {
    onError: (error) => {
      console.error('Storage error:', error)
    }
  }
)

const computedDisplayColumns = computed({
  get: () => columnPreferences.value.display,
  set: (val) => {
    columnPreferences.value.display = val
    emit('update:displayColumns', val)
  }
})

const computedWrapColumns = computed({
  get: () => columnPreferences.value.wrap,
  set: (val) => {
    columnPreferences.value.wrap = val
    emit('update:wrapColumns', val)
  }
})

// Helper function to preserve column widths
function preserveColumnWidths(newColumns, existingColumns = []) {
  if (!columnPreferences.value) {
    columnPreferences.value = { display: {}, wrap: {}, widths: {} }
  }
  if (!columnPreferences.value.widths) {
    columnPreferences.value.widths = {}
  }

  return newColumns.map(col => {
    // First try to find width in storage
    const storedWidth = columnPreferences.value?.widths?.[col.id]
    // Then try current table columns
    const currentCol = props.columns?.find(c => c.id === col.id)
    // Then fallback to form properties
    const existing = existingColumns?.find(e => e.id === col.id)
    
    // Convert any non-numeric width to default
    const defaultWidth = 250
    let width = storedWidth || currentCol?.width || existing?.width || defaultWidth
    
    // If width is not a number or is 'full', use default width
    if (typeof width !== 'number' || isNaN(width)) {
      width = defaultWidth
    }
    
    return {
      ...col,
      width
    }
  })
}

// Watch for column width changes
watch(() => props.columns, (newColumns) => {
  if (!newColumns?.length) return
  
  if (!columnPreferences.value) {
    columnPreferences.value = { display: {}, wrap: {}, widths: {} }
  }
  if (!columnPreferences.value.widths) {
    columnPreferences.value.widths = {}
  }
  
  const widths = {}
  newColumns.forEach(col => {
    if (col.width) {
      widths[col.id] = col.width
    }
  })
  
  columnPreferences.value.widths = widths
}, { deep: true })

// Initialize display and wrap columns when form changes
watch(() => props.form, (newForm) => {
  if (!newForm) return
  
  const properties = candidatesProperties.value
  const storedPrefs = columnPreferences.value
  const removedProperties = newForm.removed_properties || []

  // Initialize display columns if not set
  if (!Object.keys(storedPrefs.display).length) {
    // Set all non-removed properties to visible by default
    properties.forEach((field) => {
      storedPrefs.display[field.id] = true
    })
    // Also handle removed properties
    removedProperties.forEach((field) => {
      storedPrefs.display[field.id] = false
    })
  }

  // Initialize wrap columns if not set
  if (!Object.keys(storedPrefs.wrap).length) {
    [...properties, ...removedProperties].forEach((field) => {
      storedPrefs.wrap[field.id] = false
    })
  }

  // Initialize widths if not set
  if (!Object.keys(storedPrefs.widths).length) {
    [...properties, ...removedProperties].forEach((field) => {
      const defaultWidth = 150
      storedPrefs.widths[field.id] = field.width || defaultWidth
    })
  }

  // Emit initial values
  emit('update:displayColumns', storedPrefs.display)
  emit('update:wrapColumns', storedPrefs.wrap)

  // Emit initial columns (all non-removed visible by default)
  const initialColumns = clonedeep(properties)
    .concat(removedProperties)
    .filter((field) => storedPrefs.display[field.id] !== false)

  // Preserve any existing column widths
  const columnsWithWidths = preserveColumnWidths(initialColumns, properties)
  emit('update:columns', columnsWithWidths)
}, { immediate: true })

function toggleAllColumns(fields, show) {
  fields.forEach((field) => {
    computedDisplayColumns.value[field.id] = show
  })
  onChangeDisplayColumns()
}

function onChangeDisplayColumns() {
  if (!import.meta.client) return
  const properties = clonedeep(candidatesProperties.value)
    .concat(props.form?.removed_properties || [])
    .filter((field) => computedDisplayColumns.value[field.id] === true)
  
  // Preserve existing column widths when toggling visibility
  const columnsWithWidths = preserveColumnWidths(properties, props.form.properties)
  emit('update:columns', columnsWithWidths)
}
</script> 