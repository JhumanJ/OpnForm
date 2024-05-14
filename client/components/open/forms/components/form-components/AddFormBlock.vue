<template>
  <div>
    <div class="p-4 border-b sticky top-0 z-10 bg-white">
      <div class="flex">
        <button
          class="text-gray-500 hover:text-gray-900 cursor-pointer"
          @click.prevent="closeSidebar"
        >
          <svg
            class="h-6 w-6"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M18 6L6 18M6 6L18 18"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </button>
        <div class="font-semibold inline ml-2 flex-grow truncate">
          Add Block
        </div>
      </div>
    </div>

    <div class="py-2 px-4">
      <div>
        <p class="text-gray-500 uppercase text-xs font-semibold mb-2">
          Input Blocks
        </p>
        <draggable
          :list="inputBlocks"
          :group="{ name: 'form-elements', pull: 'clone', put: false }"
          class="grid grid-cols-2 gap-2"
          :sort="false"
          :clone="handleInputClone"
          ghost-class="ghost-item"
          item-key="id"
          @start="workingFormStore.draggingNewBlock=true"
          @end="workingFormStore.draggingNewBlock=false"
        >
          <template #item="{element}">
            <div
              class="bg-gray-50 border cursor-grab hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 py-2 flex flex-col"
              role="button"
              @click.prevent="addBlock(element.name)"
            >
              <div class="mx-auto">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-gray-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                  v-html="element.icon"
                />
              </div>
              <p
                class="w-full text-xs text-gray-500 uppercase text-center font-semibold mt-1"
              >
                {{ element.title }}
              </p>
            </div>
          </template>
        </draggable>
      </div>
      <div class="border-t mt-6">
        <p class="text-gray-500 uppercase text-xs font-semibold mb-2 mt-6">
          Layout Blocks
        </p>
        <draggable
          :list="layoutBlocks"
          :group="{ name: 'form-elements', pull: 'clone', put: false }"
          class="grid grid-cols-2 gap-2"
          :sort="false"
          :clone="handleInputClone"
          ghost-class="ghost-item"
          item-key="id"
        >
          <template #item="{element}">
            <div
              class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 py-2 flex flex-col"
              role="button"
              @click.prevent="addBlock(element.name)"
            >
              <div class="mx-auto">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-gray-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                  v-html="element.icon"
                />
              </div>
              <p
                class="w-full text-xs text-gray-500 uppercase text-center font-semibold mt-1"
              >
                {{ element.title }}
              </p>
            </div>
          </template>
        </draggable>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import { computed } from "vue"

export default {
  name: "AddFormBlock",
  components: {draggable},
  props: {},

  setup() {
    const workingFormStore = useWorkingFormStore()
    const { content: form } = storeToRefs(workingFormStore)
    return {
      form,
      workingFormStore,
      selectedFieldIndex: computed(() => workingFormStore.selectedFieldIndex),
    }
  },

  data() {
    return {
      inputBlocks: [
        {
          name: "text",
          title: "Text Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>',
        },
        {
          name: "date",
          title: "Date Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        },
        {
          name: "url",
          title: "URL Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>',
        },
        {
          name: "phone_number",
          title: "Phone Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
        },
        {
          name: "email",
          title: "Email Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>',
        },
        {
          name: "checkbox",
          title: "Checkbox Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        },
        {
          name: "select",
          title: "Select Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>',
        },
        {
          name: "multi_select",
          title: "Multi-select Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>',
        },
        {
          name: "number",
          title: "Number Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>',
        },
        {
          name: "rating",
          title: "Rating Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />',
        },
        {
          name: "scale",
          title: "Scale Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />',
        },
        {
          name: "slider",
          title: "Slider Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />',
        },
        {
          name: "files",
          title: "File Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        },
        {
          name: "signature",
          title: "Signature Input",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />',
        },
      ],
      layoutBlocks: [
        {
          name: "nf-text",
          title: "Text Block",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16" />',
        },
        {
          name: "nf-page-break",
          title: "Page-break Block",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        },
        {
          name: "nf-divider",
          title: "Divider Block",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />',
        },
        {
          name: "nf-image",
          title: "Image Block",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        },
        {
          name: "nf-code",
          title: "Code Block",
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />',
        },
      ],
    }
  },

  computed: {
  },

  watch: {},

  mounted() {
    this.workingFormStore.resetBlockForm()
  },

  methods: {
    closeSidebar() {
      this.workingFormStore.closeAddFieldSidebar()
    },
    addBlock(type) {
      this.workingFormStore.addBlock(type)
    },
    handleInputClone(item) {
      return item.name
    }
  },
}
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md w-full col-span-full;
}
</style>
