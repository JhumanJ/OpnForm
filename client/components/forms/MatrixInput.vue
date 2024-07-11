<template>
 <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>
    <div class="flex mb-2">
        <div class="w-1/4"></div>
        <div class="">
            <div class="flex space-x-6 py-2" :class="columnGrids">
                <div v-for="column in columns" :key="column" class="w-4 flex justify-center">
                    {{ column }}
                </div>
            </div>
        </div>
    </div>
    <div v-for="row, index in matrixData" class="w-full flex items-center" :key="row">
        <div class="w-1/4">
            {{row.label}}
        </div>
        <div class="flex space-x-6 py-2" :class="columnGrids">
            <div v-for="option in row.options" :key="row.label+option" class="w-4 flex justify-center">
                <input
                    type="radio"
                    :value="option"
                    :aria-checked="true"
                    :name="row.label"
                    v-model="selection[row.label]"
                    @update:model-value="onSelection(row, $event)"
                    class="styled-radio"
                    id="radio-{{ row.label }}-{{ option }}"
                >
            </div>
        </div>
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
import { inputProps, useFormInput } from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"

export default {
  name: "MatrixInput",
  components: { InputWrapper },

  props: {
    ...inputProps,
    rows: {type: Array, required: true},
    columns: {type: Array, required: true},
    selectionData: {type: Object, required: true},
  },
    data() {
        return {
            selection: {}
        }
    },
  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
    },
    computed: {
        matrixData() {
            const options = this.columns
            return this.rows?.map(row => {
                return {
                    label: row,
                    options
                }
            })
        },
        columnGrids() {
            return 'grid-cols-' + this.columns?.length
        }

    },
    methods: {
        onSelection() {
            this.compVal = this.selection
        },
        handleCompValChanged() {
            this.selection = this.compVal ?? this.selectionData
        }
    },
    mounted() {
        this.handleCompValChanged()
    },

    watch: {
        compVal: {
            handler(newVal, oldVal) {
                if (!oldVal) {
                    this.handleCompValChanged()
                }
            },
            immediate: false
        }
    },
}
</script>

<style scoped>
.styled-radio {
  appearance: none;
  -webkit-appearance: none;
  background-color: #fff;
  border: 2px solid #000;
  padding: 10px;
  border-radius: 4px;
  display: inline-block;
  position: relative;
}

.styled-radio:checked {
  background-color: transparent;
  border-color: #2563EB;
}

.styled-radio:checked::after {
  content: '';
  position: absolute;
  top: 3px;
  left: 3px;
  width: 14px;
  height: 14px;
  background-color: #2563EB;
  display: block;
}
</style>