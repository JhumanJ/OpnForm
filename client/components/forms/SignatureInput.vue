<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div
      v-if="loading || file"
      :class="[
        theme.default.input,
        theme.SignatureInput.input,
        theme.SignatureInput.spacing.horizontal,
        theme.SignatureInput.spacing.vertical,
        theme.SignatureInput.fontSize,
        theme.SignatureInput.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
      ]"
      class="flex flex-wrap items-center justify-center gap-4"
    >
      <div
        v-if="loading"
        class="text-gray-600 dark:text-gray-400"
      >
        <Loader class="mx-auto h-6 w-6" />
        <p class="mt-2 text-center text-sm text-gray-500">
          {{ $t('forms.fileInput.uploadingFile') }}
        </p>
      </div>
    
      <uploaded-file
        v-else
        :key="file.url"
        :file="file"
        :theme="theme"
        :show-remove="false"
      />
    </div>

    <VueSignaturePad
      v-else
      ref="signaturePad"
      class="not-draggable"
      :class="[
        theme.default.input,
        theme.SignatureInput.input,
        theme.SignatureInput.spacing.horizontal,
        theme.SignatureInput.spacing.vertical,
        theme.SignatureInput.fontSize,
        theme.SignatureInput.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
      ]"
      height="150px"
      :name="name"
      :options="{ onEnd }"
    />

    <template #bottom_after_help>
      <small
        v-if="!file"
        :class="theme.default.help"
        class="flex-auto"
      >
        <input
          ref="actual-input"
          class="hidden"
          :multiple="false"
          type="file"
          accept=".pdf,.png,.jpg,.jpeg"
          @change="manualFileUpload"
        >
        <a
          :class="theme.default.help"
          href="#"
          @click.prevent="openFileUpload"
        >{{ $t('forms.signatureInput.uploadFileInstead') }}</a>
      </small>

      <small :class="theme.default.help">
        <a
          :class="theme.default.help"
          href="#"
          @click.prevent="clear"
        >{{ $t('forms.signatureInput.clear') }}</a>
      </small>
    </template>

    <template #error>
      <slot name="error" />
    </template>
  </InputWrapper>
</template>

<script>
import { VueSignaturePad } from 'vue-signature-pad'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { storeFile } from '~/lib/file-uploads.js'

export default {
  name: 'SignatureInput',
  components: { InputWrapper, VueSignaturePad },

  props: {
    ...inputProps,
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  data: () => ({
    file: null,
    loading: false
  }),

  watch: {
    file: {
      handler(file) {
        this.compVal = file?.url || null
      }
    }
  },
  
  mounted() {
    this.$nextTick(() => {
      if (this.$refs.signaturePad) {
        this.$refs.signaturePad.resizeCanvas()
      }
    })
  },

  methods: {
    clear() {
      this.file = null
      this.$refs.signaturePad?.clearSignature()
      this.onEnd()
    },
    onEnd() {
      if (!this.$refs.signaturePad) {
        this.form[this.name] = null
        return
      }

      if (this.disabled) {
        this.$refs.signaturePad.clearSignature()
      } else {
        /* eslint-disable-next-line */
        const { isEmpty, data } = this.$refs.signaturePad?.saveSignature()
        this.form[this.name] = !isEmpty && data ? data : null
      }
    },
    openFileUpload() {
      if (this.disabled || !this.$refs['actual-input']) return
      this.$refs['actual-input'].click()
    },
    manualFileUpload(e) {
      const files = e.target.files
      for (let i = 0; i < files.length; i++) {
        this.uploadFileToServer(files.item(i))
      }
    },
    uploadFileToServer(file) {
      if (this.disabled) return
      this.loading = true
      storeFile(file)
        .then((response) => {
          this.file = {
            file: file,
            url: file.name.split('.').slice(0, -1).join('.') + '_' + response.uuid + '.' + response.extension,
            src: this.getFileSrc(file)
          }
          this.loading = false
        })
        .catch(() => {
          this.loading = false
          this.file = null
        })
    },
    getFileSrc(file) {
      if (file.type && file.type.split('/')[0] === 'image') {
        return URL.createObjectURL(file)
      }
      return null
    }
  },
}
</script>
