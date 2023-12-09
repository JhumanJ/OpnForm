<template>
  <editor-options-panel name="Form Structure" :already-opened="true">
    <template #icon>
      <svg class="h-5 w-5" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13.8333 7.33333C14.7668 7.33333 15.2335 7.33333 15.59 7.15168C15.9036 6.99189 16.1586 6.73692 16.3183 6.42332C16.5 6.0668 16.5 5.60009 16.5 4.66667V4.16667C16.5 3.23325 16.5 2.76654 16.3183 2.41002C16.1586 2.09641 15.9036 1.84145 15.59 1.68166C15.2335 1.5 14.7668 1.5 13.8333 1.5L4.16667 1.5C3.23325 1.5 2.76654 1.5 2.41002 1.68166C2.09641 1.84144 1.84144 2.09641 1.68166 2.41002C1.5 2.76654 1.5 3.23325 1.5 4.16667L1.5 4.66667C1.5 5.60009 1.5 6.0668 1.68166 6.42332C1.84144 6.73692 2.09641 6.99189 2.41002 7.15168C2.76654 7.33333 3.23325 7.33333 4.16667 7.33333L13.8333 7.33333Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M13.8333 16.5C14.7668 16.5 15.2335 16.5 15.59 16.3183C15.9036 16.1586 16.1586 15.9036 16.3183 15.59C16.5 15.2335 16.5 14.7668 16.5 13.8333V13.3333C16.5 12.3999 16.5 11.9332 16.3183 11.5767C16.1586 11.2631 15.9036 11.0081 15.59 10.8483C15.2335 10.6667 14.7668 10.6667 13.8333 10.6667L4.16667 10.6667C3.23325 10.6667 2.76654 10.6667 2.41002 10.8483C2.09641 11.0081 1.84144 11.2631 1.68166 11.5767C1.5 11.9332 1.5 12.3999 1.5 13.3333L1.5 13.8333C1.5 14.7668 1.5 15.2335 1.68166 15.59C1.84144 15.9036 2.09641 16.1586 2.41002 16.3183C2.76654 16.5 3.23325 16.5 4.16667 16.5H13.8333Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>
    <form-fields-editor class="mt-5" />
  </editor-options-panel>
</template>

<script>
import { useWorkingFormStore } from '../../../../../stores/working_form'
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'
import FormFieldsEditor from '../FormFieldsEditor.vue'

export default {
  components: { EditorOptionsPanel, FormFieldsEditor },
  props: {},
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore
    }
  },
  data () {
    return {
    }
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
  }
}
</script>
