<template>
  <editor-options-panel name="About Submissions" :already-opened="true">
    <template #icon>
      <svg class="h-5 w-5" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M4.83333 6.08333H9M4.83333 9H11.5M4.83333 14V15.9463C4.83333 16.3903 4.83333 16.6123 4.92436 16.7263C5.00352 16.8255 5.12356 16.8832 5.25045 16.8831C5.39636 16.8829 5.56973 16.7442 5.91646 16.4668L7.90434 14.8765C8.31043 14.5517 8.51347 14.3892 8.73957 14.2737C8.94017 14.1712 9.15369 14.0963 9.37435 14.051C9.62306 14 9.88308 14 10.4031 14H12.5C13.9001 14 14.6002 14 15.135 13.7275C15.6054 13.4878 15.9878 13.1054 16.2275 12.635C16.5 12.1002 16.5 11.4001 16.5 10V5.5C16.5 4.09987 16.5 3.3998 16.2275 2.86502C15.9878 2.39462 15.6054 2.01217 15.135 1.77248C14.6002 1.5 13.9001 1.5 12.5 1.5H5.5C4.09987 1.5 3.3998 1.5 2.86502 1.77248C2.39462 2.01217 2.01217 2.39462 1.77248 2.86502C1.5 3.3998 1.5 4.09987 1.5 5.5V10.6667C1.5 11.4416 1.5 11.8291 1.58519 12.147C1.81635 13.0098 2.49022 13.6836 3.35295 13.9148C3.67087 14 4.05836 14 4.83333 14Z"
          stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>

    <text-input name="submit_button_text" class="mt-4"
                :form="form"
                label="Text of Submit Button"
                :required="true"
    />

    <toggle-switch-input name="editable_submissions" :form="form" class="mt-4"
                         help="Gives user a unique url to update their submission"
    >
      <template #label>
        Editable submissions
        <pro-tag class="ml-1" />
      </template>
    </toggle-switch-input>
    <text-input v-if="form.editable_submissions" name="editable_submissions_button_text"
                :form="form"
                label="Text of editable submissions button"
                :required="true"
    />

    <flat-select-input :form="submissionOptions" name="databaseAction" label="Database Submission Action"
                       :options="[
                         {name:'Create new record (default)', value:'create'},
                         {name:'Update Record (or create if no match)', value:'update'}
                       ]" :required="true" help="Create a new record or update an existing one"
    >
      <template #selected="{option,optionName}">
        <div class="flex items-center truncate mr-6">
          {{ optionName }}
          <pro-tag v-if="option === 'update'" class="ml-2" />
        </div>
      </template>
      <template #option="{option, selected}">
        <span class="flex hover:text-white">
          <p class="flex-grow hover:text-white">
            {{ option.name }} <template v-if="option.value === 'update'"><pro-tag /></template>
          </p>
          <span v-if="selected" class="absolute inset-y-0 right-0 flex items-center pr-4 dark:text-white">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
              />
            </svg>
          </span>
        </span>
      </template>
    </flat-select-input>

    <v-transition>
      <div v-if="submissionOptions.databaseAction == 'update' && filterableFields.length">
        <select-input v-if="filterableFields.length" :form="form" name="database_fields_update"
                      label="Properties to check on update" :options="filterableFields" :required="true"
                      :multiple="true"
        />
        <div class="-mt-3 mb-3 text-gray-400 dark:text-gray-500">
          <small>If the submission has the same value(s) as a previous one for the selected
            column(s), we will update it, instead of creating a new one.
            <a href="#"
               @click.prevent="crisp.openHelpdeskArticle('how-to-update-a-page-on-form-submission-1t1jwmn')"
            >More
              info here.</a>
          </small>
        </div>
      </div>
    </v-transition>

    <select-input :form="submissionOptions" name="submissionMode" label="Post Submission Action"
                  :options="[
                    {name:'Show Success page', value:'default'},
                    {name:'Redirect', value:'redirect'}
                  ]" :required="true" help="Show a message, or redirect to a URL"
    >
      <template #selected="{option,optionName}">
        <div class="flex items-center truncate mr-6">
          {{ optionName }}
          <pro-tag v-if="option === 'redirect'" class="ml-2" />
        </div>
      </template>
      <template #option="{option, selected}">
        <span class="flex hover:text-white">
          <p class="flex-grow hover:text-white">
            {{ option.name }} <template v-if="option.value === 'redirect'"><pro-tag /></template>
          </p>
          <span v-if="selected" class="absolute inset-y-0 right-0 flex items-center pr-4">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
              />
            </svg>
          </span>
        </span>
      </template>
    </select-input>
    <template v-if="submissionOptions.submissionMode === 'redirect'">
      <text-input name="redirect_url"
                  :form="form"
                  label="Redirect URL"
                  :required="true" help="On submit, redirects to that URL"
      />
    </template>
    <template v-else>
      <toggle-switch-input name="re_fillable" :form="form" class="mt-4"
                           label="Allow users to fill the form again"
      />
      <text-input v-if="form.re_fillable" name="re_fill_button_text"
                  :form="form"
                  label="Text of re-start button"
                  :required="true"
      />
      <rich-text-area-input name="submitted_text"
                            :form="form"
                            label="Text After Submission"
                            :required="false"
      />
    </template>
  </editor-options-panel>
</template>

<script>
import { useWorkingFormStore } from '../../../../../stores/working_form'
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'
import ProTag from '~/components/global/ProTag.vue'
import VTransition from '~/components/global/transitions/VTransition.vue'

export default {
  components: {EditorOptionsPanel, ProTag, VTransition},
  props: {},
  setup () {
    const workingFormStore = useWorkingFormStore()
    const {content: form} = storeToRefs(workingFormStore)
    return {
      form,
      workingFormStore,
      crisp: useCrisp()
    }
  },
  data () {
    return {
      submissionOptions: {}
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
    },
    /**
     * Used for the update record on submission. Lists all visible fields on which you can filter records to update
     * on submission instead of creating
     */
    filterableFields () {
      if (this.submissionOptions.databaseAction !== 'update') return []
      return this.form.properties.filter((field) => {
        return !field.hidden && !['files', 'signature', 'multi_select'].includes(field.type)
      }).map((field) => {
        return {
          name: field.name,
          value: field.id
        }
      })
    }
  },

  watch: {
    form: {
      handler () {
        if (this.form) {
          this.submissionOptions = {
            submissionMode: this.form.redirect_url ? 'redirect' : 'default',
            databaseAction: this.form.database_fields_update ? 'update' : 'create'
          }
        }
      },
      deep: true
    },
    submissionOptions: {
      deep: true,
      handler: function (val) {
        if (val.submissionMode === 'default') {
          this.form.redirect_url = null
        }

        if (val.databaseAction === 'create') {
          this.form.database_fields_update = null
        }
      }
    }
  }
}
</script>
