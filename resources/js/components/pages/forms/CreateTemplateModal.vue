<template>
    <modal :show="show" @close="$emit('close')">
      <form @submit.prevent="createTemplate" @keydown="templateForm.onKeydown($event)">
        <div class="-m-6">
            <div class="p-6">
              <h2 class="text-nt-blue text-3xl font-bold mb-6">
                  Create template
              </h2>
              <p>
                  New template will be create from your form <span class="font-semibold">{{form.title}}</span>.
                  Template will be public for all to create form quickly.
              </p>
            </div>
            <div class="border-t py-4 px-6">
              <text-input name="name" :form="templateForm" class="mt-4" label="Title" :required="true" />
              <text-input name="slug" :form="templateForm" class="mt-4" label="Slug" :required="true" />
              <rich-text-area-input name="description" :form="templateForm" class="mt-4" label="Description" :required="true" />
              <text-input name="image_url" :form="templateForm" class="mt-4" label="Image" :required="true" />
              <questions-editor name="questions" :form="templateForm" class="mt-4" label="Frequently asked questions" />
            </div>
            <div class="flex justify-end mt-4 pb-5 px-6">
              <v-button class="mr-2" :loading="templateForm.busy">Create</v-button>
              <v-button color="gray" shade="light" @click.prevent="$emit('close')">Close</v-button>
            </div>
        </div>
      </form>
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
  