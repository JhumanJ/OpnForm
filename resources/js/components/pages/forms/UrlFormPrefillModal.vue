<template>
  <modal :show="show" @close="$emit('close')">
    <template #icon>
      <svg class="w-10 h-10 text-blue" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17 27C16.0681 27 15.6022 27 15.2346 26.8478C14.7446 26.6448 14.3552 26.2554 14.1522 25.7654C14 25.3978 14 24.9319 14 24V17.2C14 16.0799 14 15.5198 14.218 15.092C14.4097 14.7157 14.7157 14.4097 15.092 14.218C15.5198 14 16.0799 14 17.2 14H24C24.9319 14 25.3978 14 25.7654 14.1522C26.2554 14.3552 26.6448 14.7446 26.8478 15.2346C27 15.6022 27 16.0681 27 17M24.2 34H30.8C31.9201 34 32.4802 34 32.908 33.782C33.2843 33.5903 33.5903 33.2843 33.782 32.908C34 32.4802 34 31.9201 34 30.8V24.2C34 23.0799 34 22.5198 33.782 22.092C33.5903 21.7157 33.2843 21.4097 32.908 21.218C32.4802 21 31.9201 21 30.8 21H24.2C23.0799 21 22.5198 21 22.092 21.218C21.7157 21.4097 21.4097 21.7157 21.218 22.092C21 22.5198 21 23.0799 21 24.2V30.8C21 31.9201 21 32.4802 21.218 32.908C21.4097 33.2843 21.7157 33.5903 22.092 33.782C22.5198 34 23.0799 34 24.2 34Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>
    <template #title>
      <span>Url Form Prefill</span>
      <pro-tag class="ml-4 pb-3" />
    </template>

    <div class="p-4">
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
