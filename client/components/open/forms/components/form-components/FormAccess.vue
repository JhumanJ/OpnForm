<template>
  <editor-options-panel name="Form Access" :already-opened="false">
    <template #icon>
      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
        />
      </svg>
    </template>
    <text-input name="password" :form="form" class="mt-4"
                label="Form Password" help="Leave empty to disable password"
    />
    <date-input :with-time="true" name="closes_at" class="mt-4"
                :form="form"
                label="Close form on a scheduled date"
                help="Leave empty to keep the form open"
                :required="false"
    />
    <rich-text-area-input v-if="form.closes_at || form.visibility=='closed'" name="closed_text"
                          :form="form" class="mt-4"
                          label="Closed form text"
                          help="This message will be shown when the form will be closed"
                          :required="false"
    />
    <text-input name="max_submissions_count" native-type="number" :min="1" :form="form"
                label="Limit number of submissions" placeholder="Max submissions" class="mt-4"
                help="Leave empty for unlimited submissions"
                :required="false"
    />
    <rich-text-area-input v-if="form.max_submissions_count && form.max_submissions_count > 0"
                          name="max_submissions_reached_text" class="mt-4"
                          :form="form"
                          label="Max Submissions reached text"
                          help="This message will be shown when the form will have the maximum number of submissions"
                          :required="false"
    />
  </editor-options-panel>
</template>

<script>
import { useWorkingFormStore } from '../../../../../stores/working_form'
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'

export default {
  components: { EditorOptionsPanel },
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
  },
  watch: {},
  mounted () {
  },
  methods: {}
}
</script>
