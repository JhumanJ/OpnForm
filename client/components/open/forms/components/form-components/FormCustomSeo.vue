<template>
  <SettingsSection
    name="Link Settings"
    icon="i-heroicons-link"
  >  
    <h4 class="font-semibold mt-4 border-t pt-4">
      SEO & Social Sharing - Meta <ProTag
        class="ml-2"
        upgrade-modal-title="Upgrade to Enhance Your Form's SEO"
        upgrade-modal-description="Explore advanced SEO features in the editor on our Free plan. Upgrade to fully implement custom meta tags, Open Graph data, and improved search visibility. Boost your form's online presence and attract more respondents with our premium SEO toolkit."
      />
    </h4>
    <p class="text-gray-500 text-sm">
      Customize the image and text that appear when you share your form on other
      sites (Open Graph).
    </p>
    <select-input
      v-if="useFeatureFlag('custom_domains')"
      v-model="form.custom_domain"
      :clearable="true"
      :disabled="customDomainOptions.length <= 0"
      :options="customDomainOptions"
      name="type"
      class="mt-4 max-w-xs"
      label="Form Domain"
      placeholder="yourdomain.com"
    />
    <template v-if="form.seo_meta">
      <text-input
        v-model="form.seo_meta.page_title"
        name="page_title"
        class="mt-4 max-w-xs"
        label="Page Title"
        help="Max 60 characters recommended"
      />
      <text-area-input
        v-model="form.seo_meta.page_description"
        name="page_description"
        class="max-w-xs"
        label="Page Description"
        help="Between 150 and 160 characters"
      />
      <div class="flex gap-4">
        <image-input
          v-model="form.seo_meta.page_thumbnail"
          name="page_thumbnail"
          class="flex-grow"
          label="Thumbnail Image"
          help="og:image - 1200px X 630px"
        />
        <image-input
          v-model="form.seo_meta.page_favicon"
          name="page_favicon"
          class="flex-grow"
          label="Favicon Image"
          help="Public form page favicon"
        />
      </div>
    </template>

    <div class="w-full border-t pt-4 mt-4">
      <h4 class="font-semibold">
        Link Privacy
      </h4>
      <p class="text-gray-500 text-sm mb-4">
        Disable to prevent Google from listing your form in search results.
      </p>
      <ToggleSwitchInput
        name="can_be_indexed"
        :form="form"
        label="Indexable by Google"
      />
    </div>
  </SettingsSection>
</template>

<script>
import ProTag from '~/components/global/ProTag.vue'
import { useWorkingFormStore } from '../../../../../stores/working_form'

export default {
  components: {
    ProTag
  },
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      workspacesStore: useWorkspacesStore(),
      form: storeToRefs(workingFormStore).content
    }
  },
  computed: {
    workspace() {
      return this.workspacesStore.getCurrent
    },
    customDomainOptions() {
      return this.workspace.custom_domains
        ? this.workspace.custom_domains.map((domain) => {
            return {
              name: domain,
              value: domain,
            }
          })
        : []
    },
  },
  mounted () {
    if (!this.form.seo_meta || Array.isArray(this.form.seo_meta))
      this.form.seo_meta = {};

    ['page_title', 'page_description', 'page_thumbnail', 'page_favicon'].forEach((keyname) => {
      if (this.form.seo_meta[keyname] === undefined)
        this.form.seo_meta[keyname] = null
    })

    if (this.form.custom_domain && this.workspace?.custom_domains && !this.workspace.custom_domains.find((item) => { return item === this.form.custom_domain })) {
      this.form.custom_domain = null
    }
  }
}
</script>
