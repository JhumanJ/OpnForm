<template>
  <div>
    <div v-if="loadingDuplicate || loadingDelete" class="pr-4 pt-2">
      <loader class="h-6 w-6 mx-auto"/>
    </div>
    <dropdown v-else class="inline" dusk="nav-dropdown">
      <template #trigger="{toggle}">
        <v-button color="light-gray" class="mr-2" @click="toggle">
          <svg class="w-4 h-4 inline -mt-1" viewBox="0 0 16 4" fill="none"
              xmlns="http://www.w3.org/2000/svg">
            <path
                d="M8.00016 2.83366C8.4604 2.83366 8.8335 2.46056 8.8335 2.00033C8.8335 1.54009 8.4604 1.16699 8.00016 1.16699C7.53993 1.16699 7.16683 1.54009 7.16683 2.00033C7.16683 2.46056 7.53993 2.83366 8.00016 2.83366Z"
                stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            <path
                d="M13.8335 2.83366C14.2937 2.83366 14.6668 2.46056 14.6668 2.00033C14.6668 1.54009 14.2937 1.16699 13.8335 1.16699C13.3733 1.16699 13.0002 1.54009 13.0002 2.00033C13.0002 2.46056 13.3733 2.83366 13.8335 2.83366Z"
                stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            <path
                d="M2.16683 2.83366C2.62707 2.83366 3.00016 2.46056 3.00016 2.00033C3.00016 1.54009 2.62707 1.16699 2.16683 1.16699C1.70659 1.16699 1.3335 1.54009 1.3335 2.00033C1.3335 2.46056 1.70659 2.83366 2.16683 2.83366Z"
                stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </v-button>
      </template>
      <router-link :to="{name:'forms.show_public', params: {slug: form.slug}}" target="_blank"
                    class="block sm:hidden px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                    v-track.view_form_click="{form_id:form.id, form_slug:form.slug}"
        >
        <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path
            d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        View form
      </router-link>
      <a href="#"
            class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
            v-track.duplicate_form_click="{form_id:form.id, form_slug:form.slug}"
            @click.prevent="duplicateForm"
        >
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
            />
        </svg>
        Duplicate form
      </a>
      <a href="#"
            class="block block px-4 py-2 text-md text-red-600 hover:bg-red-50 flex items-center"
            v-track.delete_form_click="{form_id:form.id, form_slug:form.slug}"
            @click.prevent="alertConfirm('Do you really want to delete this form?',deleteForm)"
        >
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
        </svg>
        Delete form
      </a>
      <a href="#" v-if="user.admin"
            class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
            @click.prevent="showCreateTemplateModal=true"
        >
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
        </svg>
        Create Template
      </a>
    </dropdown>
    
    <create-template-modal :form="form" :show="showCreateTemplateModal" @close="showCreateTemplateModal=false"/>
  </div>
</template>
  
<script>
import axios from 'axios'
import {mapGetters, mapState} from 'vuex'
import Dropdown from '../../../common/Dropdown'
import CreateTemplateModal from '../CreateTemplateModal'

export default {
    name: 'ExtraMenu',
    components: { Dropdown, CreateTemplateModal },
    props: {
      form: { type: Object, required: true }
    },

    data: () => ({
      loadingDuplicate: false,
      loadingDelete: false,
      showCreateTemplateModal: false
    }),

    computed: {
      ...mapGetters({
        user: 'auth/user'
      }),
      formEndpoint: () => '/api/open/forms/{id}',
    },

    methods: {
      duplicateForm() {
        if (this.loadingDuplicate) return
        this.loadingDuplicate = true
        axios.post(this.formEndpoint.replace('{id}', this.form.id) + '/duplicate').then((response) => {
          this.$store.commit('open/forms/addOrUpdate', response.data.new_form)
          this.$router.push({name: 'forms.show', params: {slug: response.data.new_form.slug}})
          this.alertSuccess('Form was successfully duplicated.')
          this.loadingDuplicate = false
        })
      },
      deleteForm() {
        if (this.loadingDelete) return
        this.loadingDelete = true
        axios.delete(this.formEndpoint.replace('{id}', this.form.id)).then(() => {
          this.$store.commit('open/forms/remove', this.form)
          this.$router.push({name: 'home'})
          this.alertSuccess('Form was deleted.')
          this.loadingDelete = false
        })
      },
    }
}
</script>
  