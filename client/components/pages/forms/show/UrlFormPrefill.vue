<template>
  <div class="flex">
    <v-button
      v-track.url_form_prefill_click="{
        form_id: form.id,
        form_slug: form.slug,
      }"
      class="w-full"
      color="light-gray"
      @click="showUrlFormPrefillModal = true"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 mr-2 text-blue-600 inline"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M17 16v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2h2m3-4H9a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1m-1 4l-3 3m0 0l-3-3m3 3V3"
        />
      </svg>
      Url pre-fill
    </v-button>

    <modal
      :show="showUrlFormPrefillModal"
      @close="showUrlFormPrefillModal = false"
    >
      <template #icon>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-10 h-10 text-blue"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 16v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2h2m3-4H9a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1m-1 4l-3 3m0 0l-3-3m3 3V3"
          />
        </svg>
      </template>
      <template #title>
        <span>Url Form Prefill</span>
      </template>

      <div
        ref="content"
        class="p-4"
      >
        <p>
          Create dynamic links when sharing your form (whether it's embedded or
          not), that allows you to prefill your form fields. You can use this to
          personalize the form when sending it to multiple contacts for
          instance.
        </p>

        <h3 class="mt-6 border-t text-xl font-semibold mb-4 pt-6">
          How does it work?
        </h3>

        <p>
          Complete your form below and fill only the fields you want to prefill.
          You can even leave the required fields empty.
        </p>

        <div class="rounded-lg p-5 bg-gray-100 dark:bg-gray-900 mt-4">
          <open-form
            v-if="form"
            :theme="theme"
            :loading="false"
            :form="form"
            :fields="form.properties"
            :mode="FormMode.PREFILL"
            @submit="generateUrl"
          >
            <template #submit-btn="{ submitForm }">
              <v-button
                class="mt-2 px-8 mx-1"
                @click.prevent="submitForm"
              >
                Generate Pre-filled URL
              </v-button>
            </template>
          </open-form>
        </div>

        <template v-if="prefillFormData">
          <h3 class="mt-6 text-xl font-semibold mb-4 pt-6">
            Your Prefill url
          </h3>
          <form-url-prefill
            :form="form"
            :form-data="prefillFormData"
            :extra-query-param="extraQueryParam"
          />
        </template>
      </div>
    </modal>
  </div>
</template>

<script>
import ThemeBuilder from "~/lib/forms/themes/ThemeBuilder"
import FormUrlPrefill from "../../../open/forms/components/FormUrlPrefill.vue"
import OpenForm from "../../../open/forms/OpenForm.vue"
import { FormMode } from "~/lib/forms/FormModeStrategy.js"

export default {
  name: "UrlFormPrefill",
  components: { FormUrlPrefill, OpenForm },
  props: {
    form: { type: Object, required: true },
    extraQueryParam: { type: String, default: "" },
  },

  data: () => ({
    prefillFormData: null,
    showUrlFormPrefillModal: false,
  }),

  computed: {
    theme () {
      return new ThemeBuilder(this.form.theme, {
        size: this.form.size,
        borderRadius: this.form.border_radius
      }).getAllComponents()
    },
    FormMode() {
      return FormMode
    }
  },

  methods: {
    generateUrl(formData) {
      this.prefillFormData = formData
      this.$nextTick().then(() => {
        if (this.$refs.content) {
          this.$refs.content.parentElement.parentElement.parentElement.scrollTop =
            this.$refs.content.offsetHeight
        }
      })
    },
  },
}
</script>
