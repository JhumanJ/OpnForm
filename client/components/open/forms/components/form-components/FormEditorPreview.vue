<template>
  <!--   Form Preview (desktop only)   -->
  <div ref="parent"
    class="bg-gray-50 dark:bg-notion-dark-light hidden md:flex flex-grow p-5 flex-col items-center overflow-y-scroll"
  >
    <div class="border rounded-lg bg-white dark:bg-notion-dark w-full block transition-all max-w-5xl">
      <transition enter-active-class="linear duration-100 overflow-hidden"
                  enter-from-class="max-h-0"
                  enter-to-class="max-h-56"
                  leave-active-class="linear duration-100 overflow-hidden"
                  leave-from-class="max-h-56"
                  leave-to-class="max-h-0"
      >
        <div v-if="(form.logo_picture || form.cover_picture)">
          <div v-if="form.cover_picture">
            <div id="cover-picture"
                 class="max-h-56 rounded-t-lg w-full overflow-hidden flex items-center justify-center"
            >
              <img alt="Cover Picture" :src="coverPictureSrc(form.cover_picture)" class="w-full"/>
            </div>
          </div>
          <div v-if="form.logo_picture" class="w-full mx-auto p-5 relative"
               :class="{'pt-20':!form.cover_picture, 'max-w-lg': form && (form.width === 'centered')}"
          >
            <img alt="Logo Picture" :src="coverPictureSrc(form.logo_picture)"
                 :class="{'top-5':!form.cover_picture, '-top-10':form.cover_picture}"
                 class="max-w-60 h-20 object-contain absolute left-5 transition-all"
            />
          </div>
        </div>
      </transition>
      <open-complete-form ref="form-preview" class="w-full mx-auto py-5 px-3" :class="{'max-w-lg': form && (form.width === 'centered')}"
                          :creating="creating"
                          :form="form"
                          :admin-preview="true"
                          @restarted="previewFormSubmitted=false"
                          @submitted="previewFormSubmitted=true"
      />
    </div>
    <p class="text-center text-xs text-gray-400 dark:text-gray-600 mt-1">
      Form Preview <span v-if="creating"
                         class="font-normal text-gray-400 dark:text-gray-600 text-xs"
      >- Answers won't be saved</span>
      <br>
      <span v-if="previewFormSubmitted && !form.re_fillable">
        <a href="#" @click.prevent="$refs['form-preview'].restart()">Restart Form
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-nt-blue inline" viewBox="0 0 20 20"
               fill="currentColor"
          >
            <path fill-rule="evenodd"
                  d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                  clip-rule="evenodd"
            />
          </svg>
        </a>
      </span>
    </p>
  </div>
</template>

<script>
import VSwitch from '../../../../forms/components/VSwitch.vue'
import OpenCompleteForm from '../../OpenCompleteForm.vue'
import {handleDarkMode} from "~/lib/forms/public-page.js"
import { default as _has } from 'lodash/has'

export default {
  components: { OpenCompleteForm, VSwitch },
  props: {},
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore
    }
  },
  data () {
    return {
      previewFormSubmitted: false
    }
  },

  computed: {
    form: {
      get () {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set (value) {
        this.workingFormStore.set(value)
      }
    },
    creating () { // returns true if we are creating a form
      return !_has(this.form, 'id')
    }
  },

  watch: {
    'form.dark_mode': {
      handler () {
        this.handleDarkMode()
      }
    }
  },

  mounted () {
    this.handleDarkMode()
  },

  methods: {
    coverPictureSrc (val) {
      try {
        // Is valid url
        new URL(val)
      } catch (_) {
        // Is file
        return URL.createObjectURL(val)
      }
      return val
    },
    handleDarkMode () {
      handleDarkMode(this.form.dark_mode, this.$refs.parent)
    }
  },
}
</script>
