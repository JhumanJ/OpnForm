<template>
  <editor-options-panel name="Custom Code" :already-opened="false" :has-pro-tag="true">
    <template #icon>
      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M14 2.26953V6.40007C14 6.96012 14 7.24015 14.109 7.45406C14.2049 7.64222 14.3578 7.7952 14.546 7.89108C14.7599 8.00007 15.0399 8.00007 15.6 8.00007H19.7305M14 17.5L16.5 15L14 12.5M10 12.5L7.5 15L10 17.5M20 9.98822V17.2C20 18.8802 20 19.7202 19.673 20.362C19.3854 20.9265 18.9265 21.3854 18.362 21.673C17.7202 22 16.8802 22 15.2 22H8.8C7.11984 22 6.27976 22 5.63803 21.673C5.07354 21.3854 4.6146 20.9265 4.32698 20.362C4 19.7202 4 18.8802 4 17.2V6.8C4 5.11984 4 4.27976 4.32698 3.63803C4.6146 3.07354 5.07354 2.6146 5.63803 2.32698C6.27976 2 7.11984 2 8.8 2H12.0118C12.7455 2 13.1124 2 13.4577 2.08289C13.7638 2.15638 14.0564 2.27759 14.3249 2.44208C14.6276 2.6276 14.887 2.88703 15.4059 3.40589L18.5941 6.59411C19.113 7.11297 19.3724 7.3724 19.5579 7.67515C19.7224 7.94356 19.8436 8.2362 19.9171 8.5423C20 8.88757 20 9.25445 20 9.98822Z"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>
    <p class="mt-4">
      The code will be injected in the <span class="font-semibold">head</span> section of your form page.
    </p>
    <code-input name="custom_code" class="mt-4"
                :form="form" help="Custom code cannot be previewed in our editor. Please test your code using
                     your actual form page (save changes beforehand)."
                label="Custom Code"
    />
  </editor-options-panel>
</template>

<script>
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'

export default {
  components: {EditorOptionsPanel},
  props: {},
  setup() {
    const workingFormStore = useWorkingFormStore()
    const {content: form} = storeToRefs(workingFormStore)
    return {
      form,
      workingFormStore
    }
  },
  data() {
    return {}
  },

  computed: {
    form: {
      get() {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set(value) {
        this.workingFormStore.set(value)
      }
    }
  },
}
</script>
