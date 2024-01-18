<template>
  <editor-options-panel name="Link Settings - SEO" :already-opened="false" :has-pro-tag="true">
    <template #icon>
      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
        />
      </svg>
    </template>
    <p class="mt-4 text-gray-500 text-sm">
      Customize the link, images and text that appear when you share your form on other sites (Open Graph).
    </p>
    <select-input v-if="customDomainAllowed" v-model="form.custom_domain" :disabled="customDomainOptions.length <= 0"
                  :options="customDomainOptions" name="type"
                  class="mt-4" label="Form Domain" placeholder="yourdomain.com"
    />
    <text-input v-model="form.seo_meta.page_title" name="page_title" class="mt-4"
                label="Page Title" help="Under or approximately 60 characters"
    />
    <text-area-input v-model="form.seo_meta.page_description" name="page_description" class="mt-4"
                     label="Page Description" help="Between 150 and 160 characters"
    />
    <image-input v-model="form.seo_meta.page_thumbnail" name="page_thumbnail" class="mt-4"
                 label="Page Thumbnail Image" help="Also know as og:image - 1200 X 630"
    />
  </editor-options-panel>
</template>

<script>
import {useWorkingFormStore} from '../../../../../stores/working_form'
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'

export default {
  components: {EditorOptionsPanel},
  props: {},
  setup() {
    const workingFormStore = useWorkingFormStore()
    return {
      workspacesStore: useWorkspacesStore(),
      workingFormStore
    }
  },
  data() {
    return {}
  },
  computed: {
    form: {
      get() {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set(value) {
        this.workingFormStore.set(value)
      }
    },
    workspace() {
      return this.workspacesStore.getCurrent
    },
    customDomainOptions() {
      return this.workspace.custom_domains ? this.workspace.custom_domains.map((domain) => {
        return {
          name: domain,
          value: domain
        }
      }) : []
    },
    customDomainAllowed() {
      return useRuntimeConfig().public.customDomainsEnabled
    }
  },
  watch: {},
  mounted() {
    ['page_title', 'page_description', 'page_thumbnail'].forEach((keyname) => {
      if (this.form.seo_meta[keyname] === undefined) {
        this.form.seo_meta[keyname] = null
      }
    })
  },
  methods: {}
}
</script>
