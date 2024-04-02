<template>
  <editor-options-panel name="Information" :already-opened="true">
    <template #icon>
      <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 13.3332V9.99984M10 6.6665H10.0083M18.3333 9.99984C18.3333 14.6022 14.6024 18.3332 10 18.3332C5.39763 18.3332 1.66667 14.6022 1.66667 9.99984C1.66667 5.39746 5.39763 1.6665 10 1.6665C14.6024 1.6665 18.3333 5.39746 18.3333 9.99984Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>
    <text-input name="title" class="mt-4"
                :form="form"
                label="Form Title"
                :required="true"
    />
    <rich-text-area-input name="description"
                          :form="form"
                          label="Description"
                          :required="false"
    />
    <select-input name="tags" label="Tags" :form="form" class="mt-4"
                  help="To organize your forms (hidden to respondents)"
                  placeholder="Select Tag(s)" :multiple="true" :allow-creation="true"
                  :options="allTagsOptions"
    />
    <select-input name="visibility" label="Visibility" :form="form" class="mt-4"
                  help="Only public form will be accessible"
                  placeholder="Select Visibility" :required="true"
                  :options="visibilityOptions"
    />
    <v-button v-if="copyFormOptions.length > 0" color="light-gray" class="w-full mt-4" @click="showCopyFormSettingsModal=true">
      <svg class="h-5 w-5 -mt-1 text-nt-blue inline mr-2" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4.16667 12.4998C3.3901 12.4998 3.00182 12.4998 2.69553 12.373C2.28715 12.2038 1.9627 11.8794 1.79354 11.471C1.66667 11.1647 1.66667 10.7764 1.66667 9.99984V4.33317C1.66667 3.39975 1.66667 2.93304 1.84833 2.57652C2.00812 2.26292 2.26308 2.00795 2.57669 1.84816C2.93321 1.6665 3.39992 1.6665 4.33334 1.6665H10C10.7766 1.6665 11.1649 1.6665 11.4711 1.79337C11.8795 1.96253 12.204 2.28698 12.3731 2.69536C12.5 3.00165 12.5 3.38993 12.5 4.1665M10.1667 18.3332H15.6667C16.6001 18.3332 17.0668 18.3332 17.4233 18.1515C17.7369 17.9917 17.9919 17.7368 18.1517 17.4232C18.3333 17.0666 18.3333 16.5999 18.3333 15.6665V10.1665C18.3333 9.23308 18.3333 8.76637 18.1517 8.40985C17.9919 8.09625 17.7369 7.84128 17.4233 7.68149C17.0668 7.49984 16.6001 7.49984 15.6667 7.49984H10.1667C9.23325 7.49984 8.76654 7.49984 8.41002 7.68149C8.09642 7.84128 7.84145 8.09625 7.68166 8.40985C7.50001 8.76637 7.50001 9.23308 7.50001 10.1665V15.6665C7.50001 16.5999 7.50001 17.0666 7.68166 17.4232C7.84145 17.7368 8.09642 17.9917 8.41002 18.1515C8.76654 18.3332 9.23325 18.3332 10.1667 18.3332Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      Copy another form's settings
    </v-button>

    <modal :show="showCopyFormSettingsModal" max-width="md" @close="showCopyFormSettingsModal=false">
      <template #icon>
        <svg class="w-10 h-10 text-blue" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M17 27C16.0681 27 15.6022 27 15.2346 26.8478C14.7446 26.6448 14.3552 26.2554 14.1522 25.7654C14 25.3978 14 24.9319 14 24V17.2C14 16.0799 14 15.5198 14.218 15.092C14.4097 14.7157 14.7157 14.4097 15.092 14.218C15.5198 14 16.0799 14 17.2 14H24C24.9319 14 25.3978 14 25.7654 14.1522C26.2554 14.3552 26.6448 14.7446 26.8478 15.2346C27 15.6022 27 16.0681 27 17M24.2 34H30.8C31.9201 34 32.4802 34 32.908 33.782C33.2843 33.5903 33.5903 33.2843 33.782 32.908C34 32.4802 34 31.9201 34 30.8V24.2C34 23.0799 34 22.5198 33.782 22.092C33.5903 21.7157 33.2843 21.4097 32.908 21.218C32.4802 21 31.9201 21 30.8 21H24.2C23.0799 21 22.5198 21 22.092 21.218C21.7157 21.4097 21.4097 21.7157 21.218 22.092C21 22.5198 21 23.0799 21 24.2V30.8C21 31.9201 21 32.4802 21.218 32.908C21.4097 33.2843 21.7157 33.5903 22.092 33.782C22.5198 34 23.0799 34 24.2 34Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </template>
      <template #title>
        Copy Settings from another form
      </template>
      <div class="p-4 min-h-[450px]">
        <p class="text-gray-600">
          If you already have another form that you like to use as a base for this form, you can do that here.
          Select another form, confirm, and we will copy all of the other form settings (except the form structure)
          to this form.
        </p>
        <select-input v-model="copyFormId" name="copy_form_id"
                      label="Copy Settings From" class="mt-3 mb-6"
                      placeholder="Choose a form" :searchable="copyFormOptions.length > 5"
                      :options="copyFormOptions"
        />
        <div class="flex">
          <v-button color="white" class="w-full mr-2" @click="showCopyFormSettingsModal=false">
            Cancel
          </v-button>
          <v-button color="blue" class="w-full" @click="copySettings">
            Confirm & Copy
          </v-button>
        </div>
      </div>
    </modal>
  </editor-options-panel>
</template>

<script>
import clonedeep from 'clone-deep'
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'
import SelectInput from '../../../../forms/SelectInput.vue'
import { default as _has } from 'lodash/has'

export default {
  components: { SelectInput, EditorOptionsPanel },
  props: {},

  setup () {
    const formsStore = useFormsStore()
    const workingFormStore = useWorkingFormStore()
    const {getAll: forms} = storeToRefs(formsStore)
    return {
      forms,
      formsStore,
      workingFormStore,
    }
  },

  data () {
    return {
      showCopyFormSettingsModal: false,
      copyFormId: null,
      visibilityOptions: [
        {
          name: "Published",
          value: "public"
        },
        {
          name: "Draft - not publicly accessible",
          value: "draft"
        },
        {
          name: "Closed - won\'t accept new submissions",
          value: "closed"
        }
      ]
    }
  },

  computed: {
    copyFormOptions () {
      return this.forms.filter((form) => {
        return this.form.id !== form.id
      }).map((form) => {
        return {
          name: form.title,
          value: form.id
        }
      })
    },
    form: {
      get () {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set (value) {
        this.workingFormStore.set(value)
      }
    },
    allTagsOptions () {
      return this.formsStore.allTags.map((tagname) => {
        return {
          name: tagname,
          value: tagname
        }
      })
    }
  },

  watch: {},

  mounted () {
  },

  methods: {
    copySettings () {
      if (this.copyFormId == null) return
      const copyForm = clonedeep(this.forms.find((form) => form.id === this.copyFormId))
      if (!copyForm) return

      // Clean copy from form
      ['title', 'description', 'properties', 'cleanings', 'views_count', 'submissions_count', 'workspace', 'workspace_id', 'updated_at',
        'share_url', 'slug', 'notion_database_url', 'id', 'database_id', 'database_fields_update', 'creator',
        'created_at', 'deleted_at', 'last_edited_human'].forEach((property) => {
        if (_has(copyForm, property)) {
          delete copyForm[property]
        }
      })

      // Apply changes
      Object.keys(copyForm).forEach((property) => {
        this.form[property] = copyForm[property]
      })
      this.showCopyFormSettingsModal = false
    }
  }
}
</script>
