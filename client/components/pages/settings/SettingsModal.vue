<template>
  <UModal
    v-model:open="isOpen"
    :ui="{
      content: 'w-full max-w-5xl h-[80vh] overflow-hidden',
    }"
  >
    <template #content>
      <div class="flex h-full">
        <!-- Left Sidebar -->
        <aside class="w-56 bg-neutral-50 border-r border-neutral-200 flex flex-col">
          <!-- Navigation Menu -->
          <nav class="flex-1 p-2 overflow-y-auto pt-4">
            <div 
              v-for="(section, sectionIndex) in menuSections" 
              :key="section.name || sectionIndex"
              :class="sectionIndex !== menuSections.length - 1 ? 'mb-6' : ''"
            >
              <!-- Section Title -->
              <h3 
                v-if="section.name"
                class="text-xs font-medium text-neutral-400 tracking-wider mb-2 px-2"
              >
                {{ section.name }}
              </h3>
              
              <!-- Menu Items -->
              <ul class="space-y-1">
                <li v-for="item in section.items" :key="item.id">
                  <UButton
                    v-bind="createNavItem(item)"
                    class="w-full justify-start"
                    @click="setActiveItem(item.id)"
                  />
                </li>
              </ul>
            </div>
          </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
          <!-- Content Body -->
          <div class="flex-1 overflow-y-auto">
            <div class="p-6">
              <!-- Dynamic slot for each menu item -->
              <slot 
                v-if="activeItem"
                :name="activeItem" 
                :item="activeItemData"
              />
              
              <!-- Default content if no slot provided -->
              <div v-else class="text-center py-12">
                <UIcon 
                  name="i-heroicons-cog-6-tooth" 
                  class="w-12 h-12 text-neutral-400 mx-auto mb-4" 
                />
                <h3 class="text-lg font-medium text-neutral-900 mb-2">
                  Select a setting
                </h3>
                <p class="text-neutral-500">
                  Choose an option from the sidebar to configure your settings.
                </p>
              </div>
            </div>
          </div>
        </main>
      </div>
    </template>
  </UModal>
</template>

<script setup>
const emit = defineEmits(['close', 'item-changed', 'update:activeTab'])

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  activeTab: {
    type: String,
    default: null
  },
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: null
  },
  headerIcon: {
    type: String,
    default: 'i-heroicons-cog-6-tooth'
  },
  menuSections: {
    type: Array,
    required: true,
    validator: (sections) => {
      return sections.every(section => 
        Array.isArray(section.items) && 
        section.items.every(item => item.id && item.label)
      )
    }
  }
})

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('close', value)
})

// Get first item ID from menu sections
function getFirstItemId() {
  if (props.menuSections.length > 0 && props.menuSections[0].items.length > 0) {
    return props.menuSections[0].items[0].id
  }
  return null
}

// Active item state - now a ref initialized from prop
const activeItem = ref(props.activeTab || getFirstItemId())

// Watch activeTab prop from parent to sync local state
watch(() => props.activeTab, (newVal) => {
  if (newVal && newVal !== activeItem.value) {
    activeItem.value = newVal
  }
})

// Get active item data
const activeItemData = computed(() => {
  if (!activeItem.value) return null
  
  for (const section of props.menuSections) {
    const item = section.items.find(item => item.id === activeItem.value)
    if (item) return item
  }
  return null
})

// Default button configuration
const defaultButtonProps = {
  variant: 'ghost',
  activeVariant: 'soft', 
  color: 'neutral',
  block: true,
}

// Helper function to apply defaults to navigation items
const createNavItem = (item) => {
  const baseItem = {
    ...defaultButtonProps,
    label: item.label,
    icon: item.icon,
    active: activeItem.value === item.id
  }
  
  // Add custom classes to darken ghost/soft variants for better visibility on neutral-50 background
  const customClasses = ['group']
  
  // For ghost variant (default), darken hover state
  if (baseItem.variant === 'ghost' && baseItem.color === 'neutral') {
    customClasses.push('hover:bg-neutral-200/80')
    baseItem.ui = {
      leadingIcon: 'text-neutral-400 group-hover:text-neutral-500'
    }
  }
  
  // For soft variant (active state), darken background
  if (baseItem.active && baseItem.activeVariant === 'soft' && baseItem.color === 'neutral') {
    customClasses.push('bg-neutral-200/90 text-neutral-800')
  }
  
  return {
    ...baseItem,
    class: customClasses.length > 0 ? customClasses.join(' ') : undefined
  }
}

// Set active item
const setActiveItem = (itemId) => {
  activeItem.value = itemId
  emit('item-changed', itemId)
  emit('update:activeTab', itemId)
}

// Reset to default item when modal opens
watch(isOpen, (newValue) => {
  if (newValue && !props.activeTab) {
    activeItem.value = getFirstItemId()
  }
})

// Watch for changes in menuSections to ensure activeItem is valid
watch(() => props.menuSections, () => {
  const currentActiveId = activeItem.value
  const isValidTab = props.menuSections.some(section => 
    section.items.some(item => item.id === currentActiveId)
  )
  
  if (!isValidTab) {
    activeItem.value = getFirstItemId()
  }
}, { deep: true })

// Watch local activeItem and emit change to parent if updated externally
watch(activeItem, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    emit('update:activeTab', newVal)
  }
})
</script> 