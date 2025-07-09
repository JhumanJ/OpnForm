<template>
  <div class="flex flex-col gap-1 w-48">
    <!-- Column Info -->
    <div class="flex items-center gap-1 border-b p-2">
      <BlockTypeIcon 
        v-if="column.columnDef.type"
        :type="column.columnDef.type"
        bg-class="bg-transparent"
        text-class="text-neutral-500"
        class="flex-shrink-0"
      />
      <span class="text-sm font-medium truncate">
        {{ column.columnDef.header }}
      </span>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-col gap-1 p-2">
      <!-- Pin Actions -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-neutral-500">Pin Column</span>
        <div class="flex items-center gap-1">
          <UButton
            size="xs"
            variant="ghost"
            :icon="getPinIcon('left')"
            :color="getColumnPreference().pinned === 'left' ? 'primary' : 'neutral'"
            @click="setPin('left')"
          />
          <UButton
            size="xs"
            variant="ghost"
            :icon="getPinIcon('right')"
            :color="getColumnPreference().pinned === 'right' ? 'primary' : 'neutral'"
            @click="setPin('right')"
          />
          <UButton
            size="xs"
            variant="ghost"
            icon="i-heroicons-x-mark"
            :color="!getColumnPreference().pinned ? 'primary' : 'neutral'"
            @click="setPin(false)"
          />
        </div>
      </div>

      <!-- Wrap Toggle -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-neutral-500">Text Wrapping</span>
        <UButton
          size="xs"
          variant="ghost"
          :icon="getColumnPreference().wrapped ? 'i-heroicons-arrows-pointing-out' : 'i-heroicons-arrows-pointing-in'"
          :color="getColumnPreference().wrapped ? 'primary' : 'neutral'"
          @click="tableState.toggleColumnWrap(column.id)"
        />
      </div>

      <!-- Visibility Toggle -->
      <div class="flex items-center justify-between">
        <span class="text-xs text-neutral-500">Visibility</span>
        <UButton
          size="xs"
          variant="ghost"
          :icon="getColumnPreference().visible ? 'i-heroicons-eye-solid' : 'i-heroicons-eye-slash-solid'"
          :color="getColumnPreference().visible ? 'primary' : 'neutral'"
          @click="tableState.toggleColumnVisibility(column.id)"
        />
      </div>
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
  tableState: {
    type: Object,
    required: true,
  },
})

// Get column preferences helper
const getColumnPreference = () => {
  return props.tableState.getColumnPreference(props.column.id) || {}
}

// Pin actions
const setPin = (position) => {
  props.tableState.setColumnPreference(props.column.id, { pinned: position })
}

const getPinIcon = (position) => {
  if (position === 'left') return 'i-heroicons-arrow-left-on-rectangle'
  if (position === 'right') return 'i-heroicons-arrow-right-on-rectangle'
  return 'i-heroicons-rectangle-stack'
}
</script> 