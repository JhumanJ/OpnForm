<template>
  <div>
    <v-button
      class="w-full"
      color="light-gray"
      v-track.regenerate_form_link_click="{form_id:form.id, form_slug:form.slug}"
      @click="showGenerateFormLinkModal=true"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600 inline" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
        />
      </svg>
      Regenerate link
    </v-button>

    <!--  Regenerate form link modal  -->
    <modal :show="showGenerateFormLinkModal" @close="showGenerateFormLinkModal=false">
      <template #icon>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
          />
        </svg>
      </template>
      <template #title>
        Generate new form link
      </template>
      <div class="p-4">
        <p>
          You can choose between two different URL formats for your form.
          <span class="font-semibold">Be careful, changing your form URL is not a reversible operation</span>.
          Make sure to udpate your form URL everywhere where it's used.
        </p>
        <div class="border-t py-4 mt-4">
          <h3 class="text-xl text-gray-700 font-semibold">
            Human Readable URL
          </h3>
          <p>If your users are going to see this url, you might want to make nice and readable. Example:</p>
          <p class="text-gray-600 border p-4 bg-gray-50 rounded-md mt-4">
            https://opnform.com/forms/contact
          </p>
          <div class="text-center mt-4">
            <v-button :loading="loadingNewLink" color="outline-blue" @click="regenerateLink('slug')">
              Generate a Human Readable URL
            </v-button>
          </div>
        </div>
        <div class="border-t pt-4 mt-4">
          <h3 class="text-xl text-gray-700 font-semibold">
            Random ID URL
          </h3>
          <p>
            If your user are not going to see your form url (if it's embedded), and if you prefer to have a random
            non-guessable URL. Example:
          </p>
          <p class="text-gray-600 p-4 border bg-gray-50 rounded-md mt-4">
            https://opnform.com/forms/b4417f9c-34ae-4421-8006-832ee47786e7
          </p>
          <div class="text-center mt-4">
            <v-button :loading="loadingNewLink" color="outline-blue" @click="regenerateLink('uuid')">
              Generate a Random ID URL
            </v-button>
          </div>
        </div>
      </div>
    </modal>

  </div>
</template>

<script>
import { computed } from 'vue'
import { useFormsStore } from '../../../../stores/forms'

export default {
  name: 'RegenerateFormLink',
  components: {},
  props: {
    form: { type: Object, required: true }
  },

  setup () {
      const formsStore = useFormsStore()
      return {
        formsStore
      }
    },

  data: () => ({
    loadingNewLink: false,
    showGenerateFormLinkModal: false,
  }),

  computed: {
    formEndpoint: () => '/open/forms/{id}',
  },

  methods: {
    regenerateLink(option) {
      if (this.loadingNewLink) return
      this.loadingNewLink = true
      opnFetch(this.formEndpoint.replace('{id}', this.form.id) + '/regenerate-link/' + option, {method:'PUT'}).then((data) => {
        this.formsStore.save(data.form)
        this.$router.push({name: 'forms-slug-show-share', params: {slug: data.form.slug}})
        useAlert().success(data.message)
        this.loadingNewLink = false
      }).finally(() => {
        this.showGenerateFormLinkModal = false
      })
    },
  }
}
</script>
