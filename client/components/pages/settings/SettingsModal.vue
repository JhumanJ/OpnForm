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
            <slot name="nav-top" />
            <ul class="space-y-1">
              <li v-for="item in registeredPages" :key="item.id">
                <UButton
                  v-bind="createNavItem(item)"
                  class="w-full justify-start"
                  @click="setActiveItem(item.id)"
                />
              </li>
            </ul>
          </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
          <!-- Content Body -->
          <div class="flex-1 overflow-y-auto">
            <div class="p-6">
                              <!-- Modal pages will register themselves and render here -->
                <slot />
                
                <!-- Default content if no pages registered -->
                <div v-if="registeredPages.length === 0" class="text-center py-12">
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

// Registered pages for auto-registration
const registeredPages = ref([])

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  activeTab: {
    type: String,
    default: null
  }
})

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('close', value)
})

// Get first item ID from menu sections
function getFirstItemId() {
  if (registeredPages.value.length > 0) {
    return registeredPages.value[0].id
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

// Reset to default item only when modal opens
watch(isOpen, (newValue, oldValue) => {
  if (newValue && !oldValue) {
    if (!props.activeTab) {
      activeItem.value = getFirstItemId()
    }
  }
})

// Watch for changes in registered pages to ensure activeItem is valid
watch(registeredPages, () => {
  const currentActiveId = activeItem.value
  const isValidTab = registeredPages.value.some(item => item.id === currentActiveId)
  
  if (!isValidTab && registeredPages.value.length > 0) {
    activeItem.value = getFirstItemId()
  }
}, { deep: true })

// Registration functions for modal pages
function registerModalPage(id, label, icon) {
  const existingIndex = registeredPages.value.findIndex(page => page.id === id)
  if (existingIndex !== -1) {
    registeredPages.value[existingIndex] = { id, label, icon }
  } else {
    registeredPages.value.push({ id, label, icon })
    if (registeredPages.value.length === 1) {
      activeItem.value = id
    }
  }
}

function unregisterModalPage(id) {
  const index = registeredPages.value.findIndex(page => page.id === id)
  if (index !== -1) {
    registeredPages.value.splice(index, 1)
    if (activeItem.value === id && registeredPages.value.length > 0) {
      activeItem.value = registeredPages.value[0].id
    }
  }
}

// Provide functions for child components (after they're defined)
provide('activeModalItem', activeItem)
provide('registerModalPage', registerModalPage)
provide('unregisterModalPage', unregisterModalPage)
</script> 