<template>
  <div
    ref="select"
    class="v-select relative"
    :class="[{ 'w-0': multiple, 'min-w-full': multiple }]"
  >
    <div
      class="inline-block w-full flex overflow-hidden"
      :style="inputStyle"
      :class="[
        theme.SelectInput.input,
        theme.SelectInput.borderRadius,
        { 
          '!ring-red-500 !ring-2 !border-transparent': hasError, 
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
          'focus-within:ring-2 focus-within:ring-opacity-100 focus-within:border-transparent': !hasError
        },
        inputClass
      ]"
    >
      <button
        type="button"
        aria-haspopup="listbox"
        aria-expanded="true"
        aria-labelledby="listbox-label"
        class="cursor-pointer w-full flex-grow relative focus:outline-none"
        :class="[
          theme.SelectInput.spacing.horizontal,
          theme.SelectInput.spacing.vertical
        ]"
        @click="toggleDropdown"
        @focus="onFocus"
        @blur="onBlur"
      >
        <div
          class="flex items-center"
          :class="[
            theme.SelectInput.minHeight
          ]"
        >
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
            >
              <slot
                name="selected"
                :option="modelValue"
                :toggle="select"
              />
            </div>
            <div
              v-else
              key="placeholder"
            >
              <slot name="placeholder">
                <div
                  class="text-gray-400 dark:text-gray-500 w-full ltr:text-left rtl:!text-right truncate ltr:pr-3 rtl:pl-3 rtl:!pr-0"
                  :class="[
                    { 'py-1': multiple && !loading },
                    theme.SelectInput.fontSize
                  ]"
                >
                  {{ placeholder }}
                </div>
              </slot>
            </div>
          </transition>
        </div>
        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 rtl:!right-auto flex items-center ltr:pr-2 rtl:pl-2 rtl:!pr-0 pointer-events-none">
          <Icon
            name="heroicons:chevron-up-down-16-solid"
            class="h-5 w-5 text-gray-500"
          />
        </span>
      </button>
      <button
        v-if="clearable && showClearButton && !isEmpty"
        class="hover:bg-gray-50 dark:hover:bg-gray-900 ltr:border-l rtl:!border-l-0 rtl:border-r px-2 flex items-center"
        :class="[theme.SelectInput.spacing.vertical]"
        @click.prevent="clear()"
      >
        <Icon
          name="heroicons:x-mark-20-solid"
          class="w-5 h-5 text-gray-500"
          width="2em"
          dynamic
        />
      </button>
    </div>
    <collapsible
      v-model="isOpen"
      class="absolute mt-1 bg-white overflow-auto dark:bg-notion-dark-light shadow-xl z-30"
      :class="[dropdownClass,theme.SelectInput.dropdown, theme.SelectInput.borderRadius]"
      @click-away="onClickAway"
    >
      <ul
        tabindex="-1"
        role="listbox"
        class="leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5 relative"
        :class="[
          { 'max-h-42': !isSearchable, 'max-h-48': isSearchable },
          theme.SelectInput.fontSize
        ]"
      >
        <div
          v-if="isSearchable"
          class="sticky top-0 z-10 flex border-b border-gray-300"
        >
          <input
            v-model="searchTerm"
            type="text"
            class="flex-grow ltr:pl-3 ltr:pr-7 rtl:!pr-3 rtl:pl-7 py-2 w-full focus:outline-none dark:text-white"
            :placeholder="allowCreation ? $t('forms.select.searchOrTypeToCreateNew') : $t('forms.select.search')"
          >
          <div
            v-if="!searchTerm"
            class="flex absolute ltr:right-0 rtl:left-0 rtl:!right-auto inset-y-0 items-center px-2 justify-center pointer-events-none"
          >
            <Icon
              name="heroicons:magnifying-glass-solid"
              class="h-5 w-5 text-gray-500 dark:text-gray-400"
            />
          </div>
          <div
            v-else
            role="button"
            class="flex absolute ltr:right-0 rtl:!right-auto rtl:left-0 inset-y-0 items-center px-2 justify-center"
            @click="searchTerm = ''"
          >
            <Icon
              name="heroicons:backspace"
              class="h-5 w-5 rtl:rotate-180 text-gray-500 dark:text-gray-400"
            />
          </div>
        </div>
        <div
          v-if="loading"
          class="w-full py-2 flex justify-center"
        >
          <Loader class="h-6 w-6 text-nt-blue mx-auto" />
        </div>
        <div
          v-if="filteredOptions.length > 0"
          class="p-1"
        >
          <li
            v-for="item in filteredOptions"
            :key="item[optionKey]"
            role="option"
            :style="optionStyle"
            :class="[
              dropdownClass,
              theme.SelectInput.option,
              theme.SelectInput.spacing.horizontal,
              theme.SelectInput.spacing.vertical,
              { 'pr-9': multiple},
            ]"
            class="text-gray-900 select-none relative cursor-pointer group hover:bg-gray-100 dark:hover:bg-gray-900 rounded focus:outline-none"
            @click="select(item)"
          >
            <slot
              name="option"
              :option="item"
              :selected="isSelected(item)"
            />
          </li>
        </div>
        <p
          v-else-if="!loading && !(allowCreation && searchTerm)"
          class="w-full text-gray-500 text-center py-2"
        >
          {{ (allowCreation ? $t('forms.select.typeSomethingToAddAnOption') : $t('forms.select.noOptionAvailable')) }}.
        </p>
        <div
          v-if="allowCreation && searchTerm"
          class="border-t border-gray-300 p-1"
        >
          <li
            role="option"
            :style="optionStyle"
            :class="[{ 'px-3 pr-9': multiple, 'px-3': !multiple },dropdownClass,theme.SelectInput.option]"
            class="text-gray-900 select-none relative py-2 cursor-pointer group hover:bg-gray-100 dark:hover:bg-gray-900 rounded focus:outline-none"
            @click="createOption(searchTerm)"
          >
            {{ $t('forms.select.create') }} <span class="px-2 bg-gray-100 border border-gray-300 rounded group-hover-text-black">{{
              searchTerm
            }}</span>
          </li>
        </div>
      </ul>
    </collapsible>
  </div>
</template>

<script>
import Collapsible from '~/components/global/transitions/Collapsible.vue'
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import debounce from 'debounce'
import Fuse from 'fuse.js'

export default {
  name: 'VSelect',
  components: {Collapsible},
  directives: {},
  props: {
    data: Array,
    modelValue: {default: null, type: [String, Number, Array, Object]},
    inputClass: {type: String, default: null},
    dropdownClass: {type: String, default: 'w-full'},
    loading: {type: Boolean, default: false},
    required: {type: Boolean, default: false},
    multiple: {type: Boolean, default: false},
    searchable: {type: Boolean, default: false},
    clearable: {type: Boolean, default: false},
    hasError: {type: Boolean, default: false},
    remote: {type: Function, default: null},
    searchKeys: {type: Array, default: () => ['name']},
    optionKey: {type: String, default: 'id'},
    emitKey: {type: String, default: null},
    color: {type: String, default: '#3B82F6'},
    placeholder: {type: String, default: null},
    uppercaseLabels: { type: Boolean, default: true },
    showClearButton: { type: Boolean, default: true },
    theme: {
      type: Object, default: () => {
        const theme = inject("theme", null)
        if (theme) {
          return theme.value
        }
        return CachedDefaultTheme.getInstance()
      }
    },
    allowCreation: {type: Boolean, default: false},
    disabled: {type: Boolean, default: false}
  },
  emits: ['update:modelValue', 'update-options', 'focus', 'blur'],
  data() {
    return {
      isOpen: false,
      searchTerm: '',
      defaultValue: this.modelValue ?? null,
      isFocused: false
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
      const fuse = new Fuse(this.data, {
        keys: this.searchKeys,
        includeScore: true
      })
      return fuse.search(this.searchTerm).filter((res) => res.score < 0.5).map((res) => {
        return res.item
      })
    },
    isSearchable() {
      return this.searchable || this.remote !== null || this.allowCreation
    },
    isEmpty() {
      return this.multiple ? !this.modelValue || this.modelValue.length === 0 : !this.modelValue
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
    onFocus(event) {
      this.isFocused = true
      this.$emit('focus', event)
    },

    onBlur(event) {
      this.isFocused = false
      this.$emit('blur', event)
    },

    toggleDropdown() {
      if (this.disabled) {
        this.isOpen = false
      } else {
        this.isOpen = !this.isOpen
        if (this.isOpen) {
          this.onFocus()
        } else {
          this.onBlur()
        }
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
          // Only remove if clearable or not the last item
          if (this.clearable || emitValue.length > 1) {
            this.$emit('update:modelValue', emitValue.filter((item) => {
              if (this.emitKey) {
                return item !== value
              }
              return item[this.optionKey] !== value && item[this.optionKey] !== value[this.optionKey]
            }))
          }
          return
        }

        emitValue.push(value)
        this.$emit('update:modelValue', emitValue)
      } else {
        // For single select, only change value if it's different or clearable
        if (this.modelValue !== value || this.clearable) {
          this.$emit('update:modelValue', this.modelValue === value && this.clearable ? null : value)
        }
      }
    },
    clear() {
      this.$emit('update:modelValue', this.multiple ? [] : null)
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
