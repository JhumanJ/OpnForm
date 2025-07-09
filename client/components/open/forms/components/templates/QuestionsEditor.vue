<template>
  <div :class="wrapperClass">
    <label
      v-if="label"
      :for="id ? id : name"
      :class="[
        theme.default.label,
        { 'uppercase text-xs': uppercaseLabels, 'text-sm': !uppercaseLabels },
      ]"
    >
      {{ label }}
      <span
        v-if="required"
        class="text-red-500 required-dot"
      >*</span>
    </label>

    <Loader
      v-if="loading"
      key="loader"
              class="h-6 w-6 text-blue-500 mx-auto"
    />
    <div
      v-else
      class="my-3"
    >
      <div
        v-for="(questionForm, quesKey) in allQuestions"
        :key="quesKey"
        class="bg-neutral-100 p-2 mb-4"
      >
        <UButton
          color="error"
          variant="outline"
          size="sm"
          class="mb-2"
          @click.prevent="onRemove(quesKey)"
          icon="i-heroicons-trash"
          label="Remove"
        />
        <text-input
          name="question"
          :form="questionForm"
          placeholder="Question title"
        />
        <rich-text-area-input
          name="answer"
          :form="questionForm"
          class="mt-4"
          placeholder="Question response"
        />
      </div>
      <UButton
        v-if="addNew"
        color="success"
        size="sm"
        class="mt-2 flex"
        @click.prevent="onAdd"
        icon="i-heroicons-plus"
        label="Add New"
      />
    </div>

    <small
      v-if="help"
      :class="theme.SelectInput.help"
    >
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error
      v-if="hasValidation"
      :form="form"
      :field-id="name"
    />
  </div>
</template>

<script>
import inputMixin from "~/mixins/forms/input.js"

export default {
  name: "QuestionsEditor",
  mixins: [inputMixin],

  props: {
    loading: { type: Boolean, default: false },
    addNew: { type: Boolean, default: true },
    questions: { type: Array, default: ()=>[] },
  },

  data() {
    return {
      allQuestions: null,
      newQuestion: {
        question: "",
        answer: "",
      },
    }
  },

  computed: {},

  watch: {},

  mounted() {
    this.allQuestions =
      this.questions.length > 0 ? this.questions : [this.newQuestion]
  },

  methods: {
    onAdd() {
      this.allQuestions.push(this.newQuestion)
    },
    onRemove(key) {
      this.allQuestions.splice(key, 1)
    },
  },
}
</script>
