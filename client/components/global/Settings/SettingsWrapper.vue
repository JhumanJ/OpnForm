<template>
  <div class="flex flex-grow h-full relative">
    <!-- Sidebar -->
    <nav class="w-64 flex-shrink-0 overflow-y-auto border-r p-4 sticky top-0 bg-gray-50">
      <ul class="space-y-2">
        <li
          v-for="section in sections"
          :key="section.name"
        >
          <UButton
            :icon="section.icon"
            :label="section.name"
            :color="activeSection === section.name ? 'primary' : 'gray'"
            :variant="activeSection === section.name ? 'soft' : 'ghost'"
            class="w-full justify-start"
          
            @click="activeSection = section.name"
          />
        </li>
      </ul>
    </nav>

    <!-- Main content -->
    <div class="flex items-start h-full px-4 md:px-8 py-4 flex-grow">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'

const sections = ref([])
const activeSection = ref('')

const registerSection = (name, icon) => {
  const existingIndex = sections.value.findIndex(section => section.name === name)
  if (existingIndex !== -1) {
    sections.value[existingIndex] = { name, icon }
  } else {
    sections.value.push({ name, icon })
    if (sections.value.length === 1) {
      activeSection.value = name
    }
  }
}

const unregisterSection = (name) => {
  const index = sections.value.findIndex(section => section.name === name)
  if (index !== -1) {
    sections.value.splice(index, 1)
    if (activeSection.value === name && sections.value.length > 0) {
      activeSection.value = sections.value[0].name
    }
  }
}

// Provide active section and registration function to child components
provide('activeSection', activeSection)
provide('registerSection', registerSection)
provide('unregisterSection', unregisterSection)
</script>