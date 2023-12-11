<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>

    <Loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
    <div v-else class="my-3">
      <div v-for="(questionForm, quesKey) in allQuestions" :key="quesKey" class="bg-gray-100 p-2 mb-4">
          <v-button color="red" size="small" class="mb-2" @click.prevent="onRemove(quesKey)">
            <svg class="h-4 w-4 text-white inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6H5ZM8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M10 11V17M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Remove
          </v-button>
          <text-input name="question" :form="questionForm" placeholder="Question title" />
          <rich-text-area-input name="answer" :form="questionForm" class="mt-4" placeholder="Question response" />
      </div>
      <v-button v-if="addNew" color="green" size="small" nativeType="button" class="mt-2 flex" @click.prevent="onAdd">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Add New
      </v-button>
    </div>

    <small v-if="help" :class="theme.SelectInput.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'

export default {
  name: 'QuestionsEditor',
  mixins: [inputMixin],

  props: {
    loading: { type: Boolean, default: false },
    addNew: { type: Boolean, default: true },
    questions: { type: Array, default: [] },
  },

  data () {
    return {
      allQuestions: null,
      newQuestion: {
        question: '',
        answer: '',
      }
    }
  },

  mounted () {
    this.allQuestions = (this.questions.length > 0) ? this.questions : [this.newQuestion]
  },

  watch: { },

  computed: { },

  methods: {
    onAdd() {
      this.allQuestions.push(this.newQuestion)
    },
    onRemove(key){
      this.allQuestions.splice(key, 1)
    }
  }
}
</script>
