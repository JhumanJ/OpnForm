<template>
  <div class="bg-white">
    <div class="flex bg-gray-50 pb-5">
      <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
        <div class="pt-4 pb-0">
          <div class="flex">
            <h2 class="flex-grow text-gray-900">
              Your Forms
            </h2>
            <v-button v-track.create_form_click :to="{name:'forms.create'}">
              <svg class="w-4 h-4 text-white inline mr-1 -mt-1" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.99996 1.1665V12.8332M1.16663 6.99984H12.8333" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Create a new form
            </v-button>
          </div>
          <small class="flex text-gray-500">Manage your forms and submissions.</small>
        </div>
      </div>
    </div>
    <div class="flex bg-white">
      <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
        <div class="mt-8 pb-0">
          <text-input v-if="forms.length > 0" class="mb-6" :form="searchForm" name="search" label="Search a form"
                placeholder="Name of form to search"
          />
          <div v-if="allTags.length > 0" class="mb-4">
            <div v-for="tag in allTags" :key="tag"
                 :class="[
                   'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset cursor-pointer mr-2',
                   {'bg-blue-50 text-blue-600 ring-blue-500/10 dark:bg-blue-400':selectedTags.includes(tag),
                    'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:ring-blue-500/10 hover:dark:bg-blue-400':!selectedTags.includes(tag)}
                 ]"
                 title="Click for filter by tag(s)"
                 @click="onTagClick(tag)"
            >
              {{ tag }}
            </div>
          </div>
          <div v-if="!formsLoading && enrichedForms.length === 0" class="flex flex-wrap justify-center max-w-4xl">
            <img loading="lazy" class="w-56"
                  :src="asset('img/pages/forms/search_notfound.png')" alt="search-not-found">
            <h3 class="w-full mt-4 text-center text-gray-900 font-semibold">No forms found</h3>
            <div v-if="isFilteringForms && enrichedForms.length === 0 && searchForm.search" class="mt-2 w-full text-center">
              Your search "{{searchForm.search}}" did not match any forms. Please try again.
            </div>
            <v-button v-if="forms.length === 0" class="mt-4" v-track.create_form_click :to="{name:'forms.create'}">
              <svg class="w-4 h-4 text-white inline mr-1 -mt-1" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.99996 1.1665V12.8332M1.16663 6.99984H12.8333" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Create a new form
            </v-button>
          </div>
          <div v-else-if="forms.length > 0" class="mb-10">
            <div v-if="enrichedForms && enrichedForms.length">
              <div v-for="(form) in enrichedForms" :key="form.id"
                  class="mt-4 p-4 flex group bg-white hover:bg-gray-50 dark:bg-notion-dark items-center"
              >
                <div class="flex-grow items-center truncate cursor-pointer" role="button" @click.prevent="viewForm(form)">
                  <span class="font-semibold text-gray-900 dark:text-white">{{ form.title }}</span>
                  <ul class="flex text-gray-500">
                    <li class="pr-1">{{ form.views_count }} view{{ form.views_count > 0 ? 's' : '' }}</li>
                    <li class="list-disc ml-6 pr-1">{{ form.submissions_count }}
                      submission{{ form.submissions_count > 0 ? 's' : '' }}
                    </li>
                    <li class="list-disc ml-6">Edited {{ form.last_edited_human }}</li>
                  </ul>
                  <div v-if="form.visibility=='draft' || (form.tags && form.tags.length > 0)" class="mt-1 flex items-center flex-wrap gap-3">
                    <span v-if="form.visibility=='draft'" 
                        class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700">
                      Draft
                    </span>
                    <span v-for="(tag,i) in form.tags" :key="tag"
                        class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700"
                    >
                      {{ tag }}
                    </span>
                  </div>
                </div>
                <extra-menu :form="form" :isMainPage="true" />
              </div>
            </div>
          </div>
          <div v-if="formsLoading" class="text-center">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
          </div>
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
import TextInput from '../components/forms/TextInput.vue'
import OpenFormFooter from '../components/pages/OpenFormFooter.vue'
import ExtraMenu from '../components/pages/forms/show/ExtraMenu.vue'

const loadForms = function () {
  store.commit('open/forms/startLoading')
  store.dispatch('open/workspaces/loadIfEmpty').then(() => {
    store.dispatch('open/forms/loadIfEmpty', store.state['open/workspaces'].currentId)
  })
}

export default {
  components: { OpenFormFooter, TextInput, ExtraMenu },

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
    },
    viewForm (form) {
      this.$router.push({name: 'forms.show', params: {slug: form.slug}})
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
