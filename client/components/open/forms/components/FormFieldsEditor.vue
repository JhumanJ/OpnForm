<template>
  <div class="relative">
    <div class="flex gap-2 sticky top-0 bg-white border-b z-10 p-4">
      <UButton
        color="gray"
        icon="i-heroicons-plus"
        class="flex-grow justify-center"
        @click.prevent="openAddFieldSidebar"
      >
        Add Block
      </UButton>
    </div>

    <div class="p-4">
      <Draggable
        v-model="form.properties"
        item-key="id"
        class="mx-auto w-full overflow-hidden rounded-md border border-gray-300 bg-white transition-colors dark:bg-notion-dark-light"
        ghost-class="bg-nt-blue-lighter"
        :animation="200"
      >
        <template #item="{ element, index }">
          <div
            class="mx-auto w-full border-gray-300 transition-colors cursor-grab"
            :class="{
              'bg-gray-100 ': element.hidden,
              'bg-white ': !element.hidden,
              'border-b': index !== form.properties.length - 1,
              ' !border-blue-400 border-b-2': element.type === 'nf-page-break',
            }"
          >
            <div
              v-if="element"
              class="group flex items-center gap-x-0.5 py-1.5 pr-1"
            >
              <BlockTypeIcon
                :type="element.type"
                class="ml-2"
              />
              <!-- Field name and type -->
              <div class="flex grow flex-col truncate">
                <EditableTag
                  class="truncate text-gray-700 min-w-16 min-h-6"
                  :model-value="element.name"
                  @update:model-value="onChangeName(element, $event)"
                >
                  <label class="w-full cursor-pointer truncate">
                    {{ element.name }}
                  </label>
                </EditableTag>
              </div>

              <UTooltip :text="element.hidden ? 'Show Block' : 'Hide Block'">
                <button
                  class="hidden !cursor-pointer rounded p-1 transition-colors hover:bg-nt-blue-lighter items-center justify-center"
                  :class="{
                    'text-gray-300 hover:text-blue-500 md:group-hover:flex': !element.hidden,
                    'text-gray-300 hover:text-gray-500 md:flex': element.hidden,
                  }"
                  @click="toggleHidden(element)"
                >
                  <template v-if="!element.hidden">
                    <Icon
                      name="heroicons:eye-solid"
                      class="h-5 w-5"
                    />
                  </template>
                  <template v-else>
                    <Icon
                      name="heroicons:eye-slash-solid"
                      class="h-5 w-5"
                    />
                  </template>
                </button>
              </UTooltip>
              <UTooltip
                v-if="!element.type.startsWith('nf-')"
                :text="element.required ? 'Make it optional' : 'Make it required'"
              >
                <button
                  class="hidden cursor-pointer rounded p-0.5 transition-colors hover:bg-nt-blue-lighter items-center px-1 justify-center"
                  :class="{
                    'md:group-hover:flex text-gray-300 hover:text-red-500': !element.required,
                    'md:flex text-red-500': element.required,
                  }"
                  @click="toggleRequired(element)"
                >
                  <div
                    class="h-6 text-center text-3xl font-bold text-inherit -mt-0.5"
                  >
                    *
                  </div>
                </button>
              </UTooltip>
              <button
                class="cursor-pointer rounded p-1 transition-colors hover:bg-nt-blue-lighter text-gray-300 hover:text-blue-500 flex items-center justify-center"
                @click="editOptions(index)"
              >
                <Icon
                  name="heroicons:cog-8-tooth-solid"
                  class="h-5 w-5"
                />
              </button>
            </div>
          </div>
        </template>
      </Draggable>
    </div>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import EditableTag from '~/components/global/EditableTag.vue'
import BlockTypeIcon from './BlockTypeIcon.vue'

export default {
  name: 'FormFieldsEditor',
  components: {
    Draggable: draggable,
    EditableTag,
    BlockTypeIcon
  },
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      form: storeToRefs(workingFormStore).content
    }
  },

  data () {
    return {

    }
  },

  mounted() {
    this.init()
  },

  methods: {
    init() {
      if (!this.form.properties) {
        return
      }
      this.form.properties = this.form.properties.map((field) => {
        // Add more field properties
        field.placeholder = field.placeholder || null
        field.prefill = field.prefill || null
        field.help = field.help || null
        field.help_position = field.help_position || "below_input"

        return field
      })
    },
    openAddFieldSidebar () {
      this.workingFormStore.openAddFieldSidebar(null)
    },
    editOptions (index) {
      this.workingFormStore.openSettingsForField(index)
    },
    onChangeName (field, newName) {
      field.name = newName
    },
    toggleHidden (field) {
      field.hidden = !field.hidden
      if (field.hidden) {
        field.required = false
      } else {
        field.generates_uuid = false
        field.generates_auto_increment_id = false
      }
    },
    toggleRequired (field) {
      field.required = !field.required
      if (field.required)
        field.hidden = false
    }
  }
}
</script>

<style lang='scss'>
.v-popover {
  .trigger {
    @apply truncate w-full;
  }
}
</style>
