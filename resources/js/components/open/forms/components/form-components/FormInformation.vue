<template>
  <collapse class="p-5 w-full border-b" :default-value="true">
    <template #title class="test">
      <h3 id="v-step-0" class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-gray-500 mr-2 -mt-1" fill="none"
             viewBox="0 0 24 24" stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        Information
      </h3>
    </template>
    <text-input name="title" class="mt-4"
                :form="form"
                label="Title of your form"
                :required="true"
    />
    <rich-text-area-input name="description"
                          :form="form"
                          label="Description"
                          :required="false"
    />
    <select-input name="tags" label="Tags" :form="form" class="mt-3 mb-6"
                help="To organize your forms (hidden to respondents)"
                placeholder="Select Tag(s)" :multiple="true" :allowCreation="true"
                :options="allTagsOptions"
    />
    <select-input name="visibility" label="Visibility" :form="form" class="mt-3 mb-6"
                help="Only public form will be accessible"
                placeholder="Select Visibility" :required="true"
                :options="visibilityOptions"
    />
    <button
      v-if="copyFormOptions.length > 0"
      class="group mt-3 cursor-pointer relative w-full rounded-lg border-transparent flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click.prevent="showCopyFormSettingsModal=true"
    >
      Copy another form's settings
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -mt-1 text-nt-blue inline" fill="none" viewBox="0 0 24 24"
           stroke="currentColor" stroke-width="2"
      >
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"
        />
      </svg>
    </button>

    <modal :show="showCopyFormSettingsModal" @close="showCopyFormSettingsModal=false">
      <div class="-m-4 sm:-mx-6">
        <div class="p-4 border-b">
          <h2 class="text-2xl font-bold z-10 truncate -mt-2 text-nt-blue">
            Copy Settings from another form
          </h2>
        </div>
        <div class="p-4">
          <p class="text-gray-600">
            If you already have another form that you like to use as a base for this form, you can do that here.
            Select another form, confirm, and we will copy all of the other form settings (except the form structure)
            to this form.
          </p>
          <select-input v-model="copyFormId" name="copy_form_id"
                        label="Copy settings from" class="mt-3 mb-6"
                        placeholder="Choose a form" :searchable="copyFormOptions.length > 5"
                        :options="copyFormOptions"
          />
          <div class="flex justify-between">
            <v-button color="blue" shade="light" @click="copySettings">
              Confirm & Copy settings
            </v-button>
            <v-button color="gray" shade="light" class="ml-1" @click="showCopyFormSettingsModal=false">
              Close
            </v-button>
          </div>
        </div>
      </div>
    </modal>
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse'
import SelectInput from '../../../../forms/SelectInput'
import { mapState } from 'vuex'
import clonedeep from 'clone-deep'

export default {
  components: { SelectInput, Collapse },
  props: {},
  data () {
    return {
      showCopyFormSettingsModal: false,
      copyFormId: null,
      visibilityOptions: [
        {
          name: "Public",
          value: "public"
        },
        {
          name: "Draft (form won't be accessible)",
          value: "draft"
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
    ...mapState({
      forms: state => state['open/forms'].content
    }),
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    allTagsOptions () {
      return this.$store.getters['open/forms/getAllTags'].map((tagname) => {
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
        'created_at', 'deleted_at'].forEach((property) => {
        if (copyForm.hasOwnProperty(property)) {
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
