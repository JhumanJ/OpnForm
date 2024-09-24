<template>
  <editor-right-sidebar
    :width-class="showAddFieldSidebar ? 'md:max-w-[15rem]' : 'md:max-w-[20rem]'"
    :show="isOpen"
  >
    <VForm
      size="sm"
      @submit.prevent=""
    >
      <transition mode="out-in">
        <form-field-edit
          v-if="showEditFieldSidebar"
          :key="editFieldIndex"
          v-motion-fade="'fade'"
        />
        <add-form-block
          v-else-if="showAddFieldSidebar"
          v-motion-fade="'fade'"
        />
      </transition>
    </VForm>
  </editor-right-sidebar>
</template>

<script>
import { computed } from "vue"
import { useWorkingFormStore } from "../../../../../stores/working_form"
import EditorRightSidebar from "../../../editors/EditorRightSidebar.vue"
import FormFieldEdit from "../../fields/FormFieldEdit.vue"
import AddFormBlock from "./AddFormBlock.vue"

export default {
  name: "FormEditorSidebar",
  components: { EditorRightSidebar, AddFormBlock, FormFieldEdit },
  props: {},
  setup() {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      editFieldIndex: computed(() => workingFormStore.selectedFieldIndex),
      showEditFieldSidebar: computed(
        () => workingFormStore.showEditFieldSidebar,
      ),
      showAddFieldSidebar: computed(() => workingFormStore.showAddFieldSidebar),
    }
  },
  data() {
    return {}
  },
  computed: {
    isOpen() {
      return this.form !== null && (this.showEditFieldSidebar || this.showAddFieldSidebar)
    },
    form: {
      get() {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set(value) {
        this.workingFormStore.set(value)
      },
    },
  },
  watch: {},
  mounted() {},
  methods: {},
}
</script>
