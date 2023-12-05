<template>
  <editor-right-sidebar :show="form && (showEditFieldSidebar || showAddFieldSidebar)">
     <form-field-edit v-if="showEditFieldSidebar" />
     <add-form-block v-else-if="showAddFieldSidebar" />
  </editor-right-sidebar>
</template>

<script>
import { computed } from 'vue'
import { useWorkingFormStore } from '../../../../../stores/working_form'
import EditorRightSidebar from '../../../editors/EditorRightSidebar.vue'
import FormFieldEdit from '../../fields/FormFieldEdit.vue'
import AddFormBlock from './AddFormBlock.vue'

export default {
 name: 'FormEditorSidebar',
 components: { EditorRightSidebar, AddFormBlock, FormFieldEdit },
 props: {},
 setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      showEditFieldSidebar : computed(() => workingFormStore.showEditFieldSidebar),
      showAddFieldSidebar : computed(() => workingFormStore.showAddFieldSidebar)
    }
  },
 data () {
   return {}
 },
 computed: {
    form: {
      get () {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set (value) {
        this.workingFormStore.set(value)
      }
    }
 },
 watch: {},
 mounted () {
 },
 methods: {}
}
</script>
