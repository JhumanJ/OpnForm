<template>
  <div
    ref="select"
    class="v-select relative"
    :class="[{ 'w-0': multiple, 'min-w-full': multiple }]"
  >
    <span class="inline-block w-full rounded-md">
      <button
        type="button"
        aria-haspopup="listbox"
        aria-expanded="true"
        aria-labelledby="listbox-label"
        class="cursor-pointer"
        :style="inputStyle"
        :class="[theme.SelectInput.input, { 'py-2': !multiple || loading, 'py-1': multiple, '!ring-red-500 !ring-2 !border-transparent': hasError, '!cursor-not-allowed !bg-gray-200': disabled }, inputClass]"
        @click="toggleDropdown"
      >
        <div :class="{ 'h-6': !multiple, 'min-h-8': multiple && !loading }">
          <transition
            name="fade"
            mode="out-in"
          >
            <Loader
              v-if="loading"
              key="loader"
              class="h-6 w-6 text-nt-blue mx-auto"
            />
            <div
              v-else-if="modelValue"
              key="value"
              class="flex"
              :class="{ 'min-h-8': multiple }"
            >
              <slot
                name="selected"
                :option="modelValue"
              />
            </div>
            <div
              v-else
              key="placeholder"
            >
              <slot name="placeholder">
                <div
                  class="text-gray-400 dark:text-gray-500 w-full text-left truncate pr-3"
                  :class="{ 'py-1': multiple && !loading }">
                  {{ placeholder }}
                </div>
              </slot>
            </div>
          </transition>
        </div>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
          <svg
            class="h-5 w-5 text-gray-400"
            viewBox="0 0 20 20"
            fill="none"
            stroke="currentColor"
          >
            <path
              d="M7 7l3-3 3 3m0 6l-3 3-3-3"
              stroke-width="1.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </span>
      </button>
    </span>
    <collapsible v-model="isOpen" @click-away="onClickAway"
      class="absolute mt-1 rounded-md bg-white dark:bg-notion-dark-light shadow-xl z-10" :class="dropdownClass">
      <ul tabindex="-1" role="listbox"
        class="rounded-md text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5 relative"
        :class="{ 'max-h-42 py-1': !isSearchable, 'max-h-48 pb-1': isSearchable }">
        <div v-if="isSearchable" class="px-2 pt-2 sticky top-0 bg-white dark-bg-notion-dark-light z-10">
          <text-input v-model="searchTerm" name="search" :color="color" :theme="theme" placeholder="Search..." />
        </div>
        <div
          v-if="loading"
          class="w-full py-2 flex justify-center"
        >
          <Loader class="h-6 w-6 text-nt-blue mx-auto" />
        </div>
        <template v-if="filteredOptions.length > 0">
          <li
            v-for="item in filteredOptions"
            :key="item[optionKey]"
            role="option"
            :style="optionStyle"
            :class="{ 'px-3 pr-9': multiple, 'px-3': !multiple }"
            class="text-gray-900 cursor-default select-none relative py-2 cursor-pointer group hover:text-white hover:bg-form-color focus:outline-none focus-text-white focus-nt-blue"
            @click="select(item)">
            <slot
              name="option"
              :option="item"
              :selected="isSelected(item)"
            />
          </li>
        </template>
        <p
          v-else-if="!loading && !(allowCreation && searchTerm)"
          class="w-full text-gray-500 text-center py-2"
        >
          {{ (allowCreation ? 'Type something to add an option' : 'No option available') }}.
        </p>
        <li
          v-if="allowCreation && searchTerm"
          role="option"
          :style="optionStyle"
          :class="{ 'px-3 pr-9': multiple, 'px-3': !multiple }"
          class="text-gray-900 cursor-default select-none relative py-2 cursor-pointer group hover:text-white dark:text-white hover:bg-form-color focus:outline-none focus-text-white focus-nt-blue"
          @click="createOption(searchTerm)">
          Create <b class="px-1 bg-gray-300 rounded group-hover-text-black">{{ searchTerm }}</b>
        </li>
      </ul>
    </collapsible>
  </div>
</template>

<script>
import Collapsible from '~/components/global/transitions/Collapsible.vue'
import { themes} from "~/lib/forms/form-themes.js"
import TextInput from '../TextInput.vue'
import debounce from 'lodash/debounce'
import Fuse from 'fuse.js'

export default {
  name: 'VSelect',
  components: { Collapsible, TextInput },
  directives: {},
  props: {
    data: Array,
    modelValue: { default: null, type: [String, Number, Array, Object] },
    inputClass: { type: String, default: null },
    dropdownClass: { type: String, default: 'w-full' },
    loading: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    hasError: { type: Boolean, default: false },
    remote: { type: Function, default: null },
    searchKeys: { type: Array, default: () => ['name'] },
    optionKey: { type: String, default: 'id' },
    emitKey: { type: String, default: null },
    color: { type: String, default: '#3B82F6' },
    placeholder: { type: String, default: null },
    uppercaseLabels: { type: Boolean, default: true },
    theme: { type: Object, default: () => themes.default },
    allowCreation: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false }
  },
  emits: ['update:modelValue', 'update-options'],
  data() {
    return {
      isOpen: false,
      searchTerm: '',
      defaultValue: this.modelValue ?? null
    }
  },
  computed: {
    optionStyle() {
      return {
        '--bg-form-color': this.color
      }
    },
    inputStyle() {
      return {
        '--tw-ring-color': this.color
      }
    },
    debouncedRemote() {
      if (this.remote) {
        return debounce(this.remote, 300)
      }
      return null
    },
    filteredOptions() {
      if (!this.data) return []
      if (!this.searchable || this.remote || this.searchTerm === '') {
        return this.data
      }

      // Fuse search
      const fuzeOptions = {
        keys: this.searchKeys
      }
      const fuse = new Fuse(this.data, fuzeOptions)
      return fuse.search(this.searchTerm).map((res) => {
        return res.item
      })
    },
    isSearchable() {
      return this.searchable || this.remote !== null || this.allowCreation
    }
  },
  watch: {
    searchTerm(val) {
      if (!this.debouncedRemote) return
      if ((this.remote && val) || (val === '' && !this.modelValue) || (val === '' && this.isOpen)) {
        return this.debouncedRemote(val)
      }
    }
  },
  methods: {
    onClickAway(event) {
      // Check that event target isn't children of dropdown
      if (this.$refs.select && !this.$refs.select.contains(event.target)) {
        this.isOpen = false
      }
    },
    isSelected(value) {
      if (!this.modelValue) return false

      if (this.emitKey && value[this.emitKey]) {
        value = value[this.emitKey]
      }

      if (this.multiple) {
        return this.modelValue.includes(value)
      }
      return this.modelValue === value
    },
    toggleDropdown() {
      if (this.disabled) {
        this.isOpen = false
      } else {
        this.isOpen = !this.isOpen
      }
      if (!this.isOpen) {
        this.searchTerm = ''
      }
    },
    select(value) {
      if (!this.multiple) {
        // Close after select
        this.toggleDropdown()
      }

      if (this.emitKey) {
        value = value[this.emitKey]
      }

      if (this.multiple) {
        const emitValue = Array.isArray(this.modelValue) ? [...this.modelValue] : []

        if (this.isSelected(value)) {
          this.$emit('update:modelValue', emitValue.filter((item) => {
            if (this.emitKey) {
              return item !== value
            }
            return item[this.optionKey] !== value && item[this.optionKey] !== value[this.optionKey]
          }))
          return
        }

        emitValue.push(value)
        this.$emit('update:modelValue', emitValue)
      } else {
        if (this.modelValue === value) {
          this.$emit('update:modelValue', this.defaultValue ?? null)
        } else {
          this.$emit('update:modelValue', value)
        }
      }
    },
    createOption(newOption) {
      if (newOption) {
        const newItem = {
          name: newOption,
          value: newOption,
          id: newOption
        }
        this.$emit('update-options', newItem)
        this.select(newItem)
        this.searchTerm = ''
      }
    }
  }
}
</script>
