<template>
  <div class="my-4">
    <OptionSelectorInput
      :key="selectorKey"
      :model-value="currentStyle"
      name="presentation_style_switch"
      :options="styleOptions"
      :multiple="false"
      :columns="2"
      @update:modelValue="onSelectStyle"
    >
      <template #label>
        <div class="flex items-center gap-0.5">
          <span class="text-neutral-700 font-semibold text-sm">Form Style</span>
          <UButton
            color="neutral"
            variant="ghost"
            size="xs"
            icon="i-heroicons-question-mark-circle"
            class="text-neutral-500"
            @click="openHelpArticle"
          />
        </div>
      </template>
    </OptionSelectorInput>

    <UModal
      v-model:open="showConfirmModal"
      :dismissible="false"
      :title="`Switch to ${pendingStyleLabel} style?`"
      :description="modalDescription"
    >
      <template #body>
        <div class="space-y-2" v-if="removalList.length">
          <div
            v-for="(item, idx) in removalList"
            :key="idx"
            class="flex items-center gap-2 p-2 border border-gray-200 rounded-md bg-gray-50/50"
          >
            <BlockTypeIcon :type="item.type" />
            <div class="flex flex-col">
              <span class="text-sm font-medium text-neutral-700">{{ item.name || item.title }}</span>
              <span class="text-xs text-neutral-500">{{ item.title }}</span>
            </div>
          </div>
        </div>
        <div v-else class="text-sm text-neutral-600">No blocks will be removed.</div>
      </template>
      <template #footer>
        <div class="flex justify-between w-full">
          <UButton color="neutral" variant="outline" label="Cancel" @click="cancelSwitch" />
          <UButton color="primary" :label="`Switch to ${pendingStyleLabel}`" @click="confirmSwitch" />
        </div>
      </template>
    </UModal>
  </div>
  
</template>

<script setup>
import blocksTypes from '~/data/blocks_types.json'
import BlockTypeIcon from '../BlockTypeIcon.vue'

const workingFormStore = useWorkingFormStore()
const formRef = storeToRefs(workingFormStore).content
const crisp = useCrisp()

const form = computed(() => formRef.value || {})
const currentStyle = computed(() => form.value.presentation_style || 'classic')

const styleOptions = [
  { name: 'classic', label: 'Classic', icon: 'i-heroicons-bars-3-16-solid', tooltip: 'Classic form: multiple inputs per page, multi-line layout, supports multiple pages and layout blocks.' },
  { name: 'focused', label: 'Focused', icon: 'i-heroicons-rectangle-stack', tooltip: 'Typeform-like, one question per step.' }
]

const showConfirmModal = ref(false)
const removalList = ref([])
const pendingStyle = ref(null)
const selectorKey = ref(0)

// No local selection state. Display follows the actual form style (currentStyle).

const pendingStyleLabel = computed(() => pendingStyle.value === 'focused' ? 'Focused' : 'Classic')

const modalDescription = computed(() => {
  const count = removalList.value.length
  return count > 0
    ? `Switching to Focused will remove ${count} block${count>1?'s':''} not supported in this mode.`
    : 'Switch to Focused style.'
})

function onSelectStyle(newVal) {
  if (!form.value) return
  const oldVal = currentStyle.value
  if (newVal === oldVal) return

  // Compute blocks to remove for both directions
  const unsupportedIn = (target) => {
    return Object.values(blocksTypes)
      .filter(def => !(def.available_in || ['classic', 'focused']).includes(target))
      .map(def => def.name)
  }

  const disallowedNames = new Set(unsupportedIn(newVal))
  const props = Array.isArray(form.value.properties) ? form.value.properties : []
  removalList.value = props
    .filter(p => p && disallowedNames.has(p.type))
    .map(p => ({ type: p.type, title: blocksTypes[p.type]?.title || p.type, name: p.name }))

  pendingStyle.value = newVal

  if (newVal === 'classic' && removalList.value.length === 0) {
    // No removal, apply immediately
    form.value.presentation_style = 'classic'
    return
  }

  if (newVal === 'focused' && removalList.value.length === 0) {
    form.value.presentation_style = 'focused'
    return
  }

  // Show modal; do not change selection until confirm
  showConfirmModal.value = true
  // Force re-mount so the internal local state of OptionSelectorInput resets to props.modelValue
  nextTick(() => { selectorKey.value++ })
}

function confirmSwitch() {
  if (!form.value) return
  const target = pendingStyle.value
  const disallowedNames = new Set(
    Object.values(blocksTypes)
      .filter(def => !(def.available_in || ['classic', 'focused']).includes(target))
      .map(def => def.name)
  )
  const props = Array.isArray(form.value.properties) ? form.value.properties : []
  form.value.properties = props.filter(p => !(p && disallowedNames.has(p.type)))
  form.value.presentation_style = target
  showConfirmModal.value = false
  pendingStyle.value = null
  removalList.value = []
}

function cancelSwitch() {
  showConfirmModal.value = false
  pendingStyle.value = null
  removalList.value = []
}

function openHelpArticle() {
  crisp.openHelpdeskArticle('9817ba02-c8dd-41fc-90ea-fc121a3b4fc5')
}
</script>


