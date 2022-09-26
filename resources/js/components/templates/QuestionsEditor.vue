<template>
    <div :class="wrapperClass">
      <label v-if="label" :for="id?id:name"
             :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
      >
        {{ label }}
        <span v-if="required" class="text-red-500 required-dot">*</span>
      </label>
  
      <loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
      <div v-else class="my-3">
        <div v-for="(questionForm, quesKey) in allQuestions" :key="quesKey" class="bg-gray-100 p-2 mb-4">
            <v-button color="red" size="small" nativeType="button" class="text-right mb-2" @click.prevent="onRemove(quesKey)">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </v-button>
            <text-input name="question" :form="questionForm" placeholder="Question title" />
            <rich-text-area-input name="answer" :form="questionForm" class="mt-4" placeholder="Question response" />
        </div>
        <v-button v-if="addNew" color="green" size="small" nativeType="button" class="mt-2 flex" @click.prevent="onAdd">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
  import inputMixin from '~/mixins/forms/input'
  import Form from 'vform'

  export default {
    name: 'QuestionsEditor',
    mixins: [inputMixin],
  
    props: {
      loading: { type: Boolean, default: false },
      addNew: { type: Boolean, default: true }
    },
    data () {
      return {
        allQuestions: [new Form({
          question: '',
          answer: '',
        })]
      }
    },
    watch: {
      allQuestions: {
        deep: true,
        handler () {
          this.compVal = this.allQuestions.map((ques) => { 
            return ques.data() 
          })
        }
      }
    },
    computed: { },
    methods: {
      onAdd() {
        this.allQuestions.push(new Form({
          question: '',
          answer: '',
        }))
      },
      onRemove(key){
        this.allQuestions.splice(key, 1)
      }
    }
  }
  </script>
  