<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>
    <div
      class="border border-gray-300 overflow-x-auto"
      :class="[
        theme.default.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
        },
      ]"
    >
      <table class="w-full table-auto">
        <thead class="">
          <tr>
            <th class="ltr:text-left rtl:text-right p-2 w-auto max-w-xs" />
            <td
              v-for="column in columns"
              :key="column"
              class="ltr:border-l rtl:border-r rtl:!border-l-0 border-gray-300 max-w-24 overflow-hidden"
            >
              <div class="p-2 w-full flex items-center justify-center text-sm">
                {{ column }}
              </div>
            </td>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="row, rowIndex in rows"
            :key="rowIndex"
            class="border-t border-gray-300"
          >
            <td class="ltr:text-left rtl:text-right w-auto max-w-24 overflow-hidden">
              <div class="w-full p-2 text-sm">
                {{ row }}
              </div>
            </td>
            <td
              v-for="column in columns"
              :key="row + column"
              class="ltr:border-l rtl:border-r rtl:!border-l-0 border-gray-300 hover:!bg-gray-100 dark:hover:!bg-gray-800"
              :class="{
                '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800 hover:!bg-gray-200 dark:hover:!bg-gray-800': disabled,
              }"
            >
              <div
                v-if="compVal"
                class="w-full flex items-center justify-center"
                role="radio"
                :aria-checked="compVal[row] === column"
                :class="[
                  theme.FlatSelectInput.spacing.vertical,
                  theme.FlatSelectInput.fontSize,
                  theme.FlatSelectInput.option,
                  {
                    '!cursor-not-allowed !bg-transparent hover:!bg-transparent dark:hover:!bg-transparent': disabled,
                  }
                ]"
                @click="onSelect(row, column)"
              >
                <RadioButtonIcon
                  :key="row+column"
                  :is-checked="compVal[row] === column"
                  :color="color"
                  :theme="theme"
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>
  <script>
  import {inputProps, useFormInput} from "./useFormInput.js"
  import InputWrapper from "./components/InputWrapper.vue"
  import RadioButtonIcon from "./components/RadioButtonIcon.vue"
  export default {
    name: "MatrixInput",
    components: {InputWrapper, RadioButtonIcon},
    props: {
      ...inputProps,
      rows: {type: Array, required: true},
      columns: {type: Array, required: true},
    },
    setup(props, context) {
      return {
        ...useFormInput(props, context),
      }
    },
    data() {
      return {
      }
    },
    computed: {},
    mounted() {
      if (!this.compVal || typeof this.compVal !== 'object') {
        this.compVal = {}
      }
    },
    methods: {
        onSelect(row, column) {
          if (this.disabled) {
            return
          }
          if (this.compVal[row] === column && !this.required) {
              this.compVal[row] = null
          } else {
              this.compVal[row] = column
          }
      },
    },
  }
  </script>