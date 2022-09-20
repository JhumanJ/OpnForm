<template>
  <collapse class="p-5 w-full" :default-value="true">
    <template #title>
      <h3 class="font-semibold text-lg relative">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-gray-500 mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
        About Submission
      </h3>
    </template>

    <text-input name="submit_button_text" class="mt-4"
                :form="form"
                label="Text of submit button"
                :required="true"
    />

    <select-input :form="submissionOptions" name="databaseAction" label="Database Submission Action"
                  :options="[
                    {name:'Create new record (default)', value:'create'},
                    {name:'Update Record (if any)', value:'update'}
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
    </select-input>

    <v-transition>
      <div v-if="submissionOptions.databaseAction == 'update' && filterableFields.length">
        <select-input v-if="filterableFields.length" :form="form" name="database_fields_update"
                      label="Properties to check on update" :options="filterableFields" :required="true"
                      :multiple="true"
        />
        <div class="-mt-3 mb-3 text-gray-400 dark:text-gray-500">
          <small>If the submission has the same value(s) as a previous one for the selected
            column(s), we will update it, instead of creating a new one.
            <a href="#" @click.prevent="$getCrisp().push(['do', 'helpdesk:article:open', ['en', 'how-to-update-a-page-on-form-submission-1t1jwmn']])">More info here.</a>
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
      <pro-tag class="float-right" />
      <checkbox-input name="use_captcha" :form="form" class="mt-4"
                      label="Protect your form with a Captcha"
                      help="If enabled we will make sure respondant is a human"
      />
      <checkbox-input name="re_fillable" :form="form" class="mt-4"
                      label="Allow users to fill the form again"
      />
      <text-input v-if="form.re_fillable" name="re_fill_button_text"
                  :form="form"
                  label="Text of re-start button"
                  :required="true"
      />
      <rich-text-area-input name="submitted_text"
                            :form="form"
                            label="Text after submission"
                            :required="false"
      />
      <date-input :with-time="true" name="closes_at"
                  :form="form"
                  label="Closing date"
                  help="If filled, then the form won't accept submissions after the given date"
                  :required="false"
      />
      <rich-text-area-input v-if="form.closes_at" name="closed_text"
                            :form="form"
                            label="Closed form text"
                            help="This message will be shown when the form will be closed"
                            :required="false"
      />
      <text-input name="max_submissions_count" native-type="number" :min="1" :form="form"
                  label="Max number of submissions"
                  help="If filled, the form will only accept X number of submissions"
                  :required="false"
      />
      <rich-text-area-input v-if="form.max_submissions_count && form.max_submissions_count > 0" name="max_submissions_reached_text"
                            :form="form"
                            label="Max Submissions reached text"
                            help="This message will be shown when the form will have the maximum number of submissions"
                            :required="false"
      />
    </template>
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse'
import ProTag from '../../../../common/ProTag'
import VTransition from '../../../../common/transitions/VTransition'

export default {
  components: { Collapse, ProTag, VTransition },
  props: {
  },
  data () {
    return {
      submissionOptions: {}
    }
  },

  computed: {
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    /**
     * Used for the update record on submission. Lists all visible fields on which you can filter records to update
     * on submission instead of creating
     */
    filterableFields () {
      if (this.submissionOptions.databaseAction !== 'update') return []
      return this.form.properties.filter((field) => {
        return !field.hidden && window.config.notion.database_filterable_types.includes(field.type)
      }).map((field) => {
        const fieldName = (field.name !== field.notion_name) ? (field.name + ' (' + field.notion_name + ')') : field.name
        return {
          name: fieldName,
          value: field.id
        }
      })
    }
  },

  watch: {
    form: {
      handler  () {
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
          this.$set(this.form, 'redirect_url', null)
        }

        if (val.databaseAction === 'create') {
          this.$set(this.form, 'database_fields_update', null)
        }
      }
    }
  },

  mounted () {
  },

  methods: {

  }
}
</script>
