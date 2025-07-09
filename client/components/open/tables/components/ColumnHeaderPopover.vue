<template>
  <div class="p-3 space-y-3 min-w-48">
    <!-- Column Info -->
    <div class="flex items-center gap-2">
      <BlockTypeIcon 
        v-if="column.columnDef.type"
        :type="column.columnDef.type"
        bg-class="bg-transparent"
        text-class="text-gray-500"
        class="flex-shrink-0"
      />
      <span class="text-sm font-medium truncate">
        {{ column.columnDef.header }}
      </span>
    </div>

    <!-- Quick Actions -->
    <div class="space-y-2">
      <!-- Pin Actions -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-gray-500">Pin Column</span>
        <div class="flex items-center gap-1">
          <UButton
            size="xs"
            variant="ghost"
            :icon="getPinIcon('left')"
            :color="getColumnPreference().pinned === 'left' ? 'primary' : 'gray'"
            @click="setPin('left')"
          />
          <UButton
            size="xs"
            variant="ghost"
            :icon="getPinIcon('right')"
            :color="getColumnPreference().pinned === 'right' ? 'primary' : 'gray'"
            @click="setPin('right')"
          />
          <UButton
            size="xs"
            variant="ghost"
            icon="i-heroicons-x-mark"
            :color="!getColumnPreference().pinned ? 'primary' : 'gray'"
            @click="setPin(false)"
          />
        </div>
      </div>

      <!-- Wrap Toggle -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-gray-500">Text Wrapping</span>
        <UButton
          size="xs"
          variant="ghost"
          :icon="getColumnPreference().wrapped ? 'i-heroicons-arrows-pointing-out' : 'i-heroicons-arrows-pointing-in'"
          :color="getColumnPreference().wrapped ? 'primary' : 'gray'"
          @click="toggleWrap"
        />
      </div>

      <!-- Visibility Toggle -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-gray-500">Visibility</span>
        <UButton
          size="xs"
          variant="ghost"
          :icon="getColumnPreference().visible ? 'i-heroicons-eye' : 'i-heroicons-eye-slash'"
          :color="getColumnPreference().visible ? 'primary' : 'gray'"
          @click="toggleVisibility"
        />
      </div>
    </div>

    <!-- Reset Button -->
    <div class="pt-2 border-t border-gray-200">
      <UButton
        size="xs"
        variant="ghost"
        color="neutral"
        label="Reset Column"
        @click="resetColumn"
      />
    </div>
  </div>
</template>

<script setup>
import BlockTypeIcon from '~/components/open/forms/components/BlockTypeIcon.vue'

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  columnPreferences: {
    type: Object,
    required: true,
  },
})

// Get column preferences helper
const getColumnPreference = () => {
  return props.columnPreferences.getColumnPreference(props.column.id) || {}
}

// Pin actions
const setPin = (position) => {
  props.columnPreferences.setColumnPreference(props.column.id, { pinned: position })
}

const getPinIcon = (position) => {
  if (position === 'left') return 'i-heroicons-arrow-left-on-rectangle'
  if (position === 'right') return 'i-heroicons-arrow-right-on-rectangle'
  return 'i-heroicons-rectangle-stack'
}

// Wrap toggle
const toggleWrap = () => {
  const pref = getColumnPreference()
  props.columnPreferences.setColumnPreference(props.column.id, { wrapped: !pref.wrapped })
}

// Visibility toggle
const toggleVisibility = () => {
  const pref = getColumnPreference()
  props.columnPreferences.setColumnPreference(props.column.id, { visible: !pref.visible })
}

// Reset column preferences
const resetColumn = () => {
  props.columnPreferences.setColumnPreference(props.column.id, {})
}
</script> 