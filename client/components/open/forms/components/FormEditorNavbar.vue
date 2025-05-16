<template>
  <div class="w-full border-b p-2 flex gap-x-2 items-center bg-white">
    <a
      v-if="backButton"
      href="#"
      class="ml-2 flex text-blue font-semibold text-sm -m-1 hover:bg-blue-500/10 rounded-md p-1 group"
      @click.prevent="$emit('go-back')"
    >
      <Icon
        name="heroicons:arrow-left-20-solid"
        class="text-blue mr-1 w-6 h-6 group-hover:-translate-x-0.5 transition-all"
      />
    </a>


    <UTabs
      id="form-editor-navbar-tabs"
      v-model="activeTab"
      :items="[
        { label: 'Build' },
        { label: 'Design'},
        { label: 'Settings'}
      ]"
    />

    <div class="flex-grow flex justify-center gap-2">
      <EditableTag
        id="form-editor-title"
        v-model="form.title"
        element="h3"
        class="font-medium py-1 text-md w-48 text-gray-500 truncate form-editor-title"
      />
      <UBadge
        v-if="form.visibility == 'draft'"
        color="yellow"
        variant="soft"
        icon="i-heroicons-pencil-square"
        label="Draft"
      />
      <UBadge
        v-else-if="form.visibility == 'closed'"
        color="gray"
        variant="soft"
        icon="i-heroicons-lock-closed-20-solid"
        label="Closed"
      />
    </div>

    <UndoRedo />

    <div
      class="flex items-stretch gap-x-2"
    >
      <UTooltip
        text="Help"
        class="items-center relative"
        :popper="{ placement: 'left' }"
      >
        <a
          v-track.form_editor_help_button_clicked
          href="#"
          class="text-sm p-2 hover:bg-gray-100 cursor-pointer rounded-lg text-gray-500 hover:text-gray-800 cursor-pointer"
          @click.prevent="crisp.openHelpdesk()"
        >
          <Icon
            name="heroicons:question-mark-circle"
            class="w-5 h-5"
          />
        </a>
      </UTooltip>
      <slot name="before-save" />
      <UTooltip :popper="{ placement: 'left' }">
        <template #text>
          <UKbd
            :value="metaSymbol"
            size="xs"
          />
          <UKbd
            value="s"
            size="xs"
          />
        </template>
        <UButton
          v-track.save_form_click
          color="primary"
          class="px-8 md:px-4 py-2"
          :loading="updateFormLoading"
          :class="saveButtonClass"
          @click="emit('save-form')"
        >
          <svg
            class="w-4 h-4 text-white inline mr-1 -mt-1"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M17 21V13H7V21M7 3V8H15M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          <template v-if="form.visibility === 'public'">
            Publish Form
          </template>
          <template v-else>
            Save Changes
          </template>
        </UButton>
      </UTooltip>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import UndoRedo from '../../editors/UndoRedo.vue'
import { useWorkingFormStore } from '~/stores/working_form'
import { useCrisp } from '~/composables/useCrisp'

defineProps({
  backButton: {
    type: Boolean,
    default: true
  },
  updateFormLoading: {
    type: Boolean,
    required: true
  },
  saveButtonClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['go-back', 'save-form'])

const { metaSymbol } = useShortcuts()
defineShortcuts({
  meta_s: {
    handler: () => emit('save-form')
  }
})

const workingFormStore = useWorkingFormStore()
const crisp = useCrisp()

const form = computed(() => workingFormStore.content)
const { activeTab } = storeToRefs(workingFormStore)
</script>