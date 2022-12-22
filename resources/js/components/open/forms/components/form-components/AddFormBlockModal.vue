<template>
  <modal :show="show" @close="close">

    <p class="text-gray-500 uppercase text-xs font-semibold mb-2">Input Blocks</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">

      <!--   Text Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('text')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Text Input</p>
      </div>
      <!--   Date Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('date')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Date Input</p>
      </div>
      <!--   Url Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('url')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">URL Input</p>
      </div>
      <!--   Phone Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('phone_number')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Phone Input</p>
      </div>

      <!--   email Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('email')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Email Input</p>
      </div>

      <!--   checkbox Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('checkbox')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Checkbox Input</p>
      </div>

      <!--   select Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('select')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Select Input</p>
      </div>

      <!--   multiselect Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('multi_select')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold">Multi-select Input</p>
      </div>

      <!--   number Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('number')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Number Input</p>
      </div>

      <!--   files Input   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('files')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">File Input</p>
      </div>

      <!--  Signature Block   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('signature')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Signature Input</p>
      </div>

    </div>

    <p class="text-gray-500 uppercase text-xs font-semibold mb-2 mt-6">Layout Blocks</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">

      <!--   Text Block   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('nf-text')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Text Block</p>
      </div>
      <!--   Page Break Block   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('nf-page-break')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold">Page-break Block</p>
      </div>
      <!--   Divider Block   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('nf-divider')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Divider block</p>
      </div>
      <!--   Image Block   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('nf-image')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Image Block</p>
      </div>
      <!--   Code Block   -->
      <div
        class="bg-gray-50 border hover:bg-gray-100 dark:bg-gray-900 rounded-md dark:hover:bg-gray-800 p-2 flex flex-col"
        role="button" @click.prevent="addBlock('nf-code')"
      >
        <div class="mx-auto py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
          </svg>
        </div>
        <p class="w-full text-xs text-gray-500 uppercase text-center font-semibold mb-4">Code Block</p>
      </div>

    </div>
  </modal>
</template>

<script>

import Form from 'vform'
import VButton from '../../../../common/Button'

export default {
  name: 'AddFormBlockModal',
  components: {VButton},
  props: {
    formBlocks: {
      type: Array,
      required: true
    },
    show: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      blockForm: null
    }
  },

  computed: {
    defaultBlockNames() {
      return {
        'text': 'Your name',
        'date': 'Date',
        'url': 'Link',
        'phone_number': 'Phone Number',
        'number': 'Number',
        'email': 'Email',
        'checkbox': 'Checkbox',
        'select': 'Select',
        'multi_select': 'Multi Select',
        'files': 'Files',
        'signature': 'Signature',
        'nf-text': 'Text Block',
        'nf-page-break': 'Page Break',
        'nf-divider': 'Divider',
        'nf-image': 'Image',
        'nf-code': 'Code Block',
      }
    }
  },

  watch: {},

  mounted() {
    this.reset()
  },

  methods: {
    reset() {
      this.blockForm = new Form({
        type: null,
        name: null
      })
    },
    addBlock(type) {
      this.blockForm.type = type
      this.blockForm.name = this.defaultBlockNames[type]
      const data = this.prefillDefault(this.blockForm.data())
      data.id = this.generateUUID()
      data.hidden = false
      if (['select', 'multi_select'].includes(this.blockForm.type)) {
        data[this.blockForm.type] = {'options': []}
      }
      this.$emit('block-added', data)
      this.close()
    },
    generateUUID() {
      let d = new Date().getTime()// Timestamp
      let d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0// Time in microseconds since page-load or 0 if unsupported
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        let r = Math.random() * 16// random number between 0 and 16
        if (d > 0) { // Use timestamp until depleted
          r = (d + r) % 16 | 0
          d = Math.floor(d / 16)
        } else { // Use microseconds since page-load if supported
          r = (d2 + r) % 16 | 0
          d2 = Math.floor(d2 / 16)
        }
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16)
      })
    },
    prefillDefault(data) {
      if (data.type === 'nf-text') {
        data.content = '<p>This is a text block.</p>'
      } else if (data.type === 'nf-page-break') {
        data.next_btn_text = 'Next'
        data.previous_btn_text = 'Previous'
      } else if (data.type === 'nf-code') {
        data.content = '<div class="text-blue-500 italic">This is a code block.</div>'
      } else if (data.type === 'signature') {
        data.help = 'Draw your signature above'
      }
      return data
    },
    close() {
      this.$emit('close')
      this.reset()
    }
  }
}
</script>
