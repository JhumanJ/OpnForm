<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label"/>
    </template>
    <div class='border' :class="[
        theme.default.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200': disabled,
        },
    ]">
     <table 
        class="w-full table-fixed overflow-hidden border border-collapse border-transparent" 
        :class="[
            theme.default.borderRadius]"
    >
      <thead class="">
      <tr class="bg-gray-200/60">
        <th>

        </th>
        <td v-for="column in columns" :key="column" class="border border-gray-300">
          <div class="p-2 w-full flex items-center justify-center capitalize text-sm truncate">
            {{ column }}
          </div>
        </td>
      </tr>
      </thead>

      <tbody>
      <tr v-for="row, rowIndex in rows" :key="rowIndex" class="">
        <td class="border border-gray-300">
          <div class="w-full flex-grow p-2 text-sm truncate">
            {{ row }}
          </div>
        </td>
        <td v-for="column in columns" :key="row + column" class="border border-gray-300">
            <div
                class="w-full flex items-center justify-center hover:bg-gray-200/40"
              v-if="compVal"
              role="radio"
              :aria-checked="compVal[row] === column"
              :class="[
                                theme.FlatSelectInput.spacing.vertical,
                                theme.FlatSelectInput.fontSize,
                                theme.FlatSelectInput.option,
                                ]"
              @click="onSelect(row, column)"
            >
              <Icon
                v-if="compVal[row] === column"
                :key="row+column"
                name="material-symbols:radio-button-checked-outline"
                class="text-inherit"
                :color="color"
                :class="[theme.FlatSelectInput.icon]"
              />
              <Icon
                v-else
                :key="row+column"
                name="material-symbols:radio-button-unchecked"
                :class="[theme.FlatSelectInput.icon,theme.FlatSelectInput.unselectedIcon]"
              />
            </div>
        </td>
      </tr>
    </tbody>
    </table>
    </div>
    <template #help>
      <slot name="help"/>
    </template>
    <template #error>
      <slot name="error"/>
    </template>
  </input-wrapper>
</template>
<script>
import {inputProps, useFormInput} from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"

export default {
  name: "MatrixInput",
  components: {InputWrapper},

  props: {
    ...inputProps,
    rows: {type: Array, required: true},
    columns: {type: Array, required: true},
  },
  data() {
    return {
    }
  },
  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },
  computed: {},
  methods: {
    onSelect(row, column) {
      if (this.compVal[row] === column) {
        this.compVal[row] = null
      } else {
        this.compVal[row] = column
      }
    },
  },
  beforeMount() {
    if (!this.compVal || typeof this.compVal !== 'object') {
      this.compVal = {}
    }
  },
}
</script>
