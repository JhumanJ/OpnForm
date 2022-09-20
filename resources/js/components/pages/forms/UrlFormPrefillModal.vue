<template>
  <modal :show="show" @close="$emit('close')">
    <div id="form-prefill-url-content" ref="content" class="px-4">
      <h2 class="text-nt-blue text-3xl font-bold mb-4 flex items-center">
        <span>Url Form Prefill</span>
        <pro-tag class="ml-4 pb-3" />
      </h2>

      <p>
        Create dynamic links when sharing your form (whether it's embedded or not), that allows you to prefill
        your form fields. You can use this to personalize the form when sending it to multiple contacts for instance.
      </p>

      <h3 class="mt-6 border-t text-xl font-semibold mb-4 pt-6">
        How does it work?
      </h3>

      <p>
        Complete your form below and fill only the fields you want to prefill. You can even leave the required fields empty.
      </p>

      <div class="rounded-lg p-5 bg-gray-100 dark:bg-gray-900 mt-4">
        <open-form v-if="form" :theme="theme" :loading="false" :show-hidden="true" :form="form" :fields="form.properties" @submit="generateUrl">
          <template #submit-btn="{submitForm}">
            <v-button class="mt-2 px-8 mx-1" @click.prevent="submitForm">
              Generate Pre-filled URL
            </v-button>
          </template>
        </open-form>
      </div>

      <template v-if="prefillFormData">
        <h3 class="mt-6 text-xl font-semibold mb-4 pt-6">
          Your Prefill url
        </h3>

        <form-url-prefill :form="form" :form-data="prefillFormData" />
      </template>

      <div class="flex justify-end mt-4">
        <v-button color="gray" shade="light" @click="$emit('close')">Close</v-button>
      </div>

    </div>
  </modal>
</template>

<script>
import FormUrlPrefill from '../../open/forms/components/FormUrlPrefill'
import ProTag from '../../common/ProTag'
import OpenForm from '../../open/forms/OpenForm'
import { themes } from '~/config/form-themes'

export default {
  name: 'UrlFormPrefillModal',
  components: { FormUrlPrefill, ProTag, OpenForm },
  props: {
    show: { type: Boolean, required: true },
    form: { type: Object, required: true }
  },

  data: () => ({
    prefillFormData: null,
    theme: themes.default
  }),

  computed: {},

  methods: {
    generateUrl (formData, onFailure) {
      this.prefillFormData = formData
      this.$nextTick().then(() => {
        this.$refs.content.parentElement.parentElement.parentElement.scrollTop = (this.$refs.content.offsetHeight - this.$refs.content.parentElement.parentElement.parentElement.offsetHeight + 50)
      })
    }
  }
}
</script>
