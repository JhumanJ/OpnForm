<template>
  <div class="space-y-4">
    <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">
          SEO & Social Sharing - Meta <ProTag
          class="ml-2"
          upgrade-modal-title="Upgrade to Enhance Your Form's SEO"
          upgrade-modal-description="Explore advanced SEO features in the editor on our Free plan. Upgrade to fully implement custom meta tags, Open Graph data, and improved search visibility. Boost your form's online presence and attract more respondents with our premium SEO toolkit."
        />
        </h3>
        <p class="mt-1 text-sm text-neutral-500">
          Customize the image and text that appear when you share your form on other sites (Open Graph).
        </p>
      </div>
      <UButton
        label="Help"
        icon="i-heroicons-question-mark-circle"
        variant="outline"
        color="neutral"
        @click="crisp.openHelpdeskArticle('how-do-i-add-custom-seo-settings-to-my-forms-url-preview-1v9y9a')"
      />
    </div>

    <SelectInput
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
        class="mt-4 max-w-xs"
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

    <div v-if="useFeatureFlag('self_hosted')" class="w-full border-t pt-4 mt-4">
      <h4 class="font-semibold">
        Custom Form URL
      </h4>
      <p class="text-gray-500 text-sm mb-4">
        Create a custom URL for your form. This will be the unique identifier in your form's URL.
      </p>
      <text-input
        :form="form"
        name="slug"
        class="mt-4 max-w-xs"
        label="Custom Form URL"
        help="Use only lowercase letters, numbers, and hyphens. Example: my-custom-form"
      />
    </div>
  </div>
</template>

<script setup>
const crisp = useCrisp()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()
const { content: form } = storeToRefs(workingFormStore)

const workspace = computed(() => workspacesStore.getCurrent)

const customDomainOptions = computed(() => {
  return workspace.value.custom_domains
    ? workspace.value.custom_domains.map((domain) => {
        return {
          name: domain,
          value: domain,
        }
      })
    : []
})

onMounted(() => {
  if (!form.value.seo_meta || Array.isArray(form.value.seo_meta))
    form.value.seo_meta = {};

  ['page_title', 'page_description', 'page_thumbnail', 'page_favicon'].forEach((keyname) => {
    if (form.value.seo_meta[keyname] === undefined)
      form.value.seo_meta[keyname] = null
  })

  if (form.value.custom_domain && workspace.value?.custom_domains && !workspace.value.custom_domains.find((item) => { return item === form.value.custom_domain })) {
    form.value.custom_domain = null
  }
})
</script>
