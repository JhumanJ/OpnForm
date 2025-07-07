<template>
  <div>
    <div class="p-2 border-b border-gray-300 sticky top-0 z-10 bg-white">
      <div class="flex items-center">
        <UButton
          size="sm"
          color="neutral"
          icon="i-heroicons-x-mark-20-solid"
          variant="outlint"
          @click="closeSidebar"
        />
        <div class="font-medium inline ml-2 flex-grow truncate">
          Add Block
        </div>
        <AiFieldGenerator
          class="py-2 px-4"
        />
      </div>
    </div>

    <div class="py-2 px-4">
      <p class="text-gray-500 text-xs font-medium my-2">
        Input Blocks
      </p>
      <draggable
        :list="inputBlocks"
        :group="{ name: 'form-elements', pull: 'clone', put: false }"
        class="flex flex-col -mx-2"
        :sort="false"
        :clone="handleInputClone"
        ghost-class="ghost-item"
        item-key="id"
        @start="workingFormStore.draggingNewBlock=true"
        @end="workingFormStore.draggingNewBlock=false"
      >
        <template #item="{element}">
          <div
            class="flex hover:bg-gray-50 rounded-md items-center gap-2 p-2 group"
            role="button"
            @click.prevent="addBlock(element.name)"
          >
            <BlockTypeIcon :type="element.name" />
            <p
              class="w-full text-sm text-gray-500"
            >
              {{ element.title }}
            </p>
            <Icon
              v-if="element.auth_required && !authenticated"
              name="heroicons:lock-closed"
              class="text-gray-400 w-4 h-4"
            />
          </div>
        </template>
      </draggable>
    </div>
    <div class="px-4 border-t mb-4">
      <p class="text-sm font-medium my-2">
        Layout Blocks
      </p>
      <draggable
        :list="layoutBlocks"
        :group="{ name: 'form-elements', pull: 'clone', put: false }"
        class="flex flex-col -mx-2"
        :sort="false"
        :clone="handleInputClone"
        ghost-class="ghost-item"
        item-key="id"
        @start="workingFormStore.draggingNewBlock=true"
        @end="workingFormStore.draggingNewBlock=false"
      >
        <template #item="{element}">
          <div
            class="flex hover:bg-gray-50 rounded-md items-center gap-2 p-2"
            role="button"
            @click.prevent="addBlock(element.name)"
          >
            <BlockTypeIcon :type="element.name" />
            <p
              class="w-full text-sm text-gray-500"
            >
              {{ element.title }}
            </p>
            <Icon
              v-if="element.auth_required && !authenticated"
              name="heroicons:lock-closed"
              class="text-gray-400 w-4 h-4"
            />
          </div>
        </template>
      </draggable>
    </div>
  </div>
</template>

<script setup>
import draggable from 'vuedraggable'
import blocksTypes from '~/data/blocks_types.json'
import BlockTypeIcon from '../BlockTypeIcon.vue'
import AiFieldGenerator from './components/AiFieldGenerator.vue'

const workingFormStore = useWorkingFormStore()
const { isAuthenticated: authenticated } = useAuthFlow()

const inputBlocks = computed(() => Object.values(blocksTypes).filter(block => !block.name.startsWith('nf-')))
const layoutBlocks = computed(() => Object.values(blocksTypes).filter(block => block.name.startsWith('nf-')))

const closeSidebar = () => {
  workingFormStore.closeAddFieldSidebar()
}

const addBlock = (type) => {
  
  workingFormStore.addBlock(type)
}

const handleInputClone = (item) => {
 
  return item.name
}

workingFormStore.resetBlockForm()
</script>

<style lang="scss" scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md w-full col-span-full;
}
</style>
