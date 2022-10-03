<template>
  <div class="flex flex-col min-h-full mt-6">
    <div class="w-full flex-grow md:w-3/5 lg:w-1/2 md:mx-auto md:max-w-2xl px-4">
      <div>
        <div class="flex flex-wrap items-center mt-6 mb-4">
          <h2 class="text-nt-blue text-3xl font-bold flex-grow">
            Your Forms
          </h2>
          <fancy-link v-track.create_form_click class="mt-4 sm:mt-0" :to="{name:'forms.create'}" color="nt-blue" @click="showCreateFormModal=true">
            Create a new form
          </fancy-link>
        </div>

        <p v-if="!formsLoading && enrichedForms.length === 0 && !isFilteringForms">
          You don't have any form yet.
        </p>
        <div v-else-if="forms.length > 0" class="mb-10">
          <text-input v-if="forms.length > 5" class="mb-6" :form="searchForm" name="search" label="Search a form"
                      placeholder="Name of form to search"
          />
          <div v-if="allTags.length > 0" class="mb-6">
            <div v-for="tag in allTags" :key="tag"
                 :class="['text-white p-2 text-xs inline rounded-lg font-semibold cursor-pointer mr-2',{'bg-gray-500 dark:bg-gray-400':selectedTags.includes(tag), 'bg-gray-300 dark:bg-gray-700':!selectedTags.includes(tag)}]"
                 title="Click for filter by tag(s)"
                 @click="onTagClick(tag)"
            >
              {{ tag }}
            </div>
          </div>
          <div v-if="enrichedForms && enrichedForms.length" class="border border border-gray-300 dark:bg-notion-dark-light rounded-md w-full">
            <div v-for="(form, index) in enrichedForms" :key="form.id"
                 class="p-4 w-full mx-auto border-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors cursor-pointer relative" :class="{'border-t':index!==0}"
            >
              <div class="items-center space-x-4 truncate">
                <p class="truncate float-left">
                  {{ form.title }} <span v-if="form.submissions_count" class="text-gray-400 ml-1">- {{
                    form.submissions_count
                  }} Submission{{ form.submissions_count > 0 ? 's' : '' }}</span>
                </p>
                <div v-if="form.tags && form.tags.length > 0" class="float-right hidden sm:block">
                  <template v-for="(tag,i) in form.tags">
                    <div v-if="i<1" :key="tag"
                         class="bg-gray-300 dark:bg-gray-700 text-white px-2 py-1 mr-2 text-xs inline rounded-lg font-semibold"
                    >
                      {{ tag }}
                    </div>
                    <div v-if="i==1" :key="tag"
                         class="bg-gray-300 dark:bg-gray-700 text-white px-2 py-1 mr-2 text-xs inline rounded-lg font-semibold"
                    >
                      {{ form.tags.length-1 }} more
                    </div>
                  </template>
                </div>
              </div>
              <router-link class="absolute inset-0"
                           :to="{params:{slug:form.slug},name:'forms.show'}"
              />
            </div>
          </div>
          <p class="text-gray-400 dark:text-gray-600 mt-2 px-4">
            You have {{ forms.length }} forms<template v-if="isFilteringForms">
              ({{ enrichedForms.length }} matching search criteria)
            </template>.
          </p>
        </div>
        <div v-if="formsLoading" class="text-center">
          <loader class="h-6 w-6 text-nt-blue mx-auto" />
        </div>
      </div>
    </div>
    <open-form-footer class="mt-8 border-t" />
  </div>
</template>

<script>
import store from '~/store'
import { mapGetters, mapState } from 'vuex'
import Fuse from 'fuse.js'
import Form from 'vform'
import TextInput from '../components/forms/TextInput'
import OpenFormFooter from '../components/pages/OpenFormFooter'

const loadForms = function () {
  store.commit('open/forms/startLoading')
  store.dispatch('open/workspaces/loadIfEmpty').then(() => {
    store.dispatch('open/forms/loadIfEmpty', store.state['open/workspaces'].currentId)
  })
}

export default {
  components: { OpenFormFooter, TextInput },

  beforeRouteEnter (to, from, next) {
    loadForms()
    next()
  },
  middleware: 'auth',

  props: {
    metaTitle: { type: String, default: 'Your Forms' },
    metaDescription: { type: String, default: 'All of your OpnForm are here. Create new forms, or update your existing one!' }
  },

  data () {
    return {
      showEditFormModal: false,
      selectedForm: null,
      searchForm: new Form({
        search: ''
      }),
      selectedTags: []
    }
  },

  mounted () {},

  methods: {
    editForm (form) {
      this.selectedForm = form
      this.showEditFormModal = true
    },
    onTagClick (tag) {
      const idx = this.selectedTags.indexOf(tag)
      if (idx === -1) {
        this.selectedTags.push(tag)
      } else {
        this.selectedTags.splice(idx, 1)
      }
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    ...mapState({
      forms: state => state['open/forms'].content,
      formsLoading: state => state['open/forms'].loading
    }),
    isFilteringForms () {
      return (this.searchForm.search !== '' && this.searchForm.search !== null) || this.selectedTags.length > 0
    },
    enrichedForms () {
      let enrichedForms = this.forms.map((form) => {
        form.workspace = this.$store.getters['open/workspaces/getById'](form.workspace_id)
        return form
      })

      // Filter by Selected Tags
      if (this.selectedTags.length > 0) {
        enrichedForms = enrichedForms.filter((item) => {
          return (item.tags && item.tags.length > 0) ? this.selectedTags.every(r => item.tags.includes(r)) : false
        })
      }

      if (!this.isFilteringForms || this.searchForm.search === '' || this.searchForm.search === null) {
        return enrichedForms
      }

      // Fuze search
      const fuzeOptions = {
        keys: [
          'title',
          'slug',
          'tags'
        ]
      }
      const fuse = new Fuse(enrichedForms, fuzeOptions)
      return fuse.search(this.searchForm.search).map((res) => {
        return res.item
      })
    },
    allTags () {
      return this.$store.getters['open/forms/getAllTags']
    }
  }
}
</script>
