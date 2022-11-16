<template>
    <modal :show="show" @close="$emit('close')">
      <template #icon>
        <svg class="w-10 h-10 text-blue" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M17 27C16.0681 27 15.6022 27 15.2346 26.8478C14.7446 26.6448 14.3552 26.2554 14.1522 25.7654C14 25.3978 14 24.9319 14 24V17.2C14 16.0799 14 15.5198 14.218 15.092C14.4097 14.7157 14.7157 14.4097 15.092 14.218C15.5198 14 16.0799 14 17.2 14H24C24.9319 14 25.3978 14 25.7654 14.1522C26.2554 14.3552 26.6448 14.7446 26.8478 15.2346C27 15.6022 27 16.0681 27 17M24.2 34H30.8C31.9201 34 32.4802 34 32.908 33.782C33.2843 33.5903 33.5903 33.2843 33.782 32.908C34 32.4802 34 31.9201 34 30.8V24.2C34 23.0799 34 22.5198 33.782 22.092C33.5903 21.7157 33.2843 21.4097 32.908 21.218C32.4802 21 31.9201 21 30.8 21H24.2C23.0799 21 22.5198 21 22.092 21.218C21.7157 21.4097 21.4097 21.7157 21.218 22.092C21 22.5198 21 23.0799 21 24.2V30.8C21 31.9201 21 32.4802 21.218 32.908C21.4097 33.2843 21.7157 33.5903 22.092 33.782C22.5198 34 23.0799 34 24.2 34Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </template>
      <template #title>
        Create template
      </template>
      <div class="p-4">
        <p>
          New template will be create from your form <span class="font-semibold">{{form.title}}</span>.
          Template will be public for all to create form quickly.
        </p>

        <form @submit.prevent="createTemplate" @keydown="templateForm.onKeydown($event)" class="mt-6">
          <div class="-m-6">
              <div class="border-t py-4 px-6">
                <text-input name="name" :form="templateForm" class="mt-4" label="Title" :required="true" />
                <text-input name="slug" :form="templateForm" class="mt-4" label="Slug" :required="true" />
                <rich-text-area-input name="description" :form="templateForm" class="mt-4" label="Description" :required="true" />
                <text-input name="image_url" :form="templateForm" class="mt-4" label="Image" :required="true" />
                <questions-editor name="questions" :form="templateForm" class="mt-4" label="Frequently asked questions" />
              </div>
              <div class="flex justify-end mt-4 pb-5 px-6">
                <v-button class="mr-2" :loading="templateForm.busy">Create</v-button>
                <v-button color="white" @click.prevent="$emit('close')">Close</v-button>
              </div>
          </div>
        </form>
      </div>
    </modal>
</template>
  
<script>
import Form from 'vform'
import QuestionsEditor from '../../templates/QuestionsEditor';

export default {
    name: 'CreateTemplateModal',
    components: { QuestionsEditor },
    props: {
      show: { type: Boolean, required: true },
      form: { type: Object, required: true }
    },

    data: () => ({
      templateForm: new Form({
        name: '',
        slug: '',
        description: '',
        image_url: '',
      })
    }),

    computed: {},

    methods: {
      async createTemplate() {
        this.templateForm.form = this.form
        await this.templateForm.post('/api/templates').then((response) => {
          this.alertSuccess('Template was successfully created.')
          this.$emit('close')
        });
      }
    }
}
</script>
  