<template>
  <Modal :show="show" :closeable="!aiForm.busy" @close="$emit('close')">
    <template #icon>
      <template v-if="state == 'default'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-blue">
          <path fill-rule="evenodd"
            d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z"
            clip-rule="evenodd" />
        </svg>
      </template>
      <template v-else-if="state == 'ai'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-blue-500">
          <path fill-rule="evenodd"
            d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z"
            clip-rule="evenodd" />
        </svg>
      </template>
    </template>
    <template #title>
      <template v-if="state == 'default'">
        Choose a base for your form
      </template>
      <template v-else-if="state == 'ai'">
        AI-powered form generator
      </template>
    </template>
    <div v-if="state == 'default'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
      <div v-track.select_form_base="{ base: 'contact-form' }" role="button"
        class="rounded-md border p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50" @click="$emit('close')">
        <div class="p-4">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-blue-500">
            <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
            <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
          </svg>
        </div>
        <p class="font-medium">
          Start from a simple contact form
        </p>
      </div>
      <div v-if="aiFeaturesEnabled" v-track.select_form_base="{ base: 'ai' }"
        class="rounded-md border p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50" role="button"
        @click="state = 'ai'">
        <div class="p-4 relative">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-blue-500">
            <path fill-rule="evenodd"
              d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z"
              clip-rule="evenodd" />
          </svg>
        </div>
        <p class="font-medium text-blue-700">
          Use our AI to create the form
        </p>
        <span class="text-xs text-gray-500">(1 min)</span>
      </div>
      <div class="rounded-md border p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50 relative">
        <div class="p-4 relative">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-blue-500">
            <path
              d="M11.25 5.337c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.036 1.007-1.875 2.25-1.875S15 2.34 15 3.375c0 .369-.128.713-.349 1.003-.215.283-.401.604-.401.959 0 .332.278.598.61.578 1.91-.114 3.79-.342 5.632-.676a.75.75 0 01.878.645 49.17 49.17 0 01.376 5.452.657.657 0 01-.66.664c-.354 0-.675-.186-.958-.401a1.647 1.647 0 00-1.003-.349c-1.035 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401.31 0 .557.262.534.571a48.774 48.774 0 01-.595 4.845.75.75 0 01-.61.61c-1.82.317-3.673.533-5.555.642a.58.58 0 01-.611-.581c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.035-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959a.641.641 0 01-.658.643 49.118 49.118 0 01-4.708-.36.75.75 0 01-.645-.878c.293-1.614.504-3.257.629-4.924A.53.53 0 005.337 15c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.036 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.369 0 .713.128 1.003.349.283.215.604.401.959.401a.656.656 0 00.659-.663 47.703 47.703 0 00-.31-4.82.75.75 0 01.83-.832c1.343.155 2.703.254 4.077.294a.64.64 0 00.657-.642z" />
          </svg>
        </div>
        <p class="font-medium">
          Start from a template
        </p>
        <NuxtLink v-track.select_form_base="{ base: 'template' }" :to="{ name: 'templates' }" class="absolute inset-0" />
      </div>
    </div>
    <div v-else-if="state == 'ai'">
      <a class="absolute top-4 left-4" href="#" role="button" @click.prevent="state = 'default'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 inline -mt-1">
          <path fill-rule="evenodd"
            d="M7.72 12.53a.75.75 0 010-1.06l7.5-7.5a.75.75 0 111.06 1.06L9.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5z"
            clip-rule="evenodd" />
        </svg>
        Back
      </a>
      <text-area-input label="Form Description" :disabled="loading ? true : null" :form="aiForm" name="form_prompt"
        help="Give us a description of the form you want to build (the more details the better)"
        placeholder="A simple contact form, with a name, email and message field" />
      <v-button class="w-full" :loading="loading" @click.prevent="generateForm">
        Generate a form
      </v-button>
      <p class="text-gray-500 text-xs text-center mt-1">
        ~60 sec
      </p>
    </div>
  </Modal>
</template>

<script>
export default {
  emits: ['close', 'form-generated'],
  props: {
    show: { type: Boolean, required: true }
  },
  setup() {
    return {
      useAlert: useAlert(),
      runtimeConfig: useRuntimeConfig()
    }
  },
  data: () => ({
    state: 'default',
    aiForm: useForm({
      form_prompt: ''
    }),
    loading: false
  }),

  computed: {
    aiFeaturesEnabled() {
      return this.runtimeConfig.public.aiFeaturesEnabled
    }
  },

  methods: {
    generateForm() {
      if (this.loading) return

      this.loading = true
      this.aiForm.post('/forms/ai/generate').then(response => {
        this.useAlert.success(response.message)
        this.fetchGeneratedForm(response.ai_form_completion_id)
      }).catch(error => {
        console.error(error)
        this.loading = false
        this.state = 'ai'
      })
    },
    fetchGeneratedForm(generationId) {
      // check every 4 seconds if form is generated
      setTimeout(() => {
        opnFetch('/forms/ai/' + generationId).then(data => {
          if (data.ai_form_completion.status === 'completed') {
            this.useAlert.success(data.message)
            this.$emit('form-generated', JSON.parse(data.ai_form_completion.result))
            this.$emit('close')
          } else if (data.ai_form_completion.status === 'failed') {
            this.useAlert.error('Something went wrong, please try again.')
            this.state = 'default'
            this.loading = false
          } else {
            this.fetchGeneratedForm(generationId)
          }
        }).catch(error => {
          if (error?.data?.message) {
            this.useAlert.error(error.data.message)
          }
          this.state = 'default'
          this.loading = false
        })
      }, 4000)
    }
  }
}
</script>
