<template>
  <div class="v-select">
    <label v-if="label"
           :class="[theme.SelectInput.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>

    <div v-on-clickaway="closeDropdown"
         class="relative"
    >
      <span class="inline-block w-full rounded-md">
        <button type="button" :dusk="dusk" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label"
                class="cursor-pointer"
                :style="inputStyle" :class="[theme.SelectInput.input,{'py-2':!multiple || loading,'py-1': multiple, 'ring-red-500 ring-2': hasError}]"
                @click="openDropdown"
        >
          <div :class="{'h-6':!multiple, 'min-h-8':multiple && !loading}">
            <transition name="fade" mode="out-in">
              <loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
              <div v-else-if="value" key="value" class="flex" :class="{'min-h-8':multiple}">
                <slot name="selected" :option="value" />
              </div>
              <div v-else key="placeholder">
                <slot name="placeholder">
                  <div class="text-gray-400 dark:text-gray-500 w-full text-left" :class="{'py-1':multiple && !loading}">
                    {{ placeholder }}
                  </div>
                </slot>
              </div>
            </transition>
          </div>
          <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
              <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
        </button></span>
      <!-- Select popover, show/hide based on select state. -->
      <div v-show="isOpen" :dusk="dusk+'_dropdown'"
           class="absolute mt-1 w-full rounded-md bg-white dark:bg-notion-dark-light shadow-lg z-10"
      >
        <ul tabindex="-1" role="listbox" aria-labelled by="listbox-label" aria-activedescendant="listbox-item-3"
            class="rounded-md text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5 relative"
            :class="{'max-h-42 py-1': !isSearchable,'max-h-48 pb-1': isSearchable}"
        >
          <div v-if="isSearchable" class="px-2 pt-2 sticky top-0 bg-white dark:bg-notion-dark-light z-10">
            <text-input name="search" :color="color" v-model="searchTerm" :theme="theme"
                        placeholder="Search..."
            />
          </div>
          <div v-if="loading" class="w-full py-2 flex justify-center">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
          </div>
          <template v-if="filteredOptions.length>0">
            <li v-for="item in filteredOptions" :key="item[optionKey]" role="option"
                class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9  cursor-pointer group hover:text-white hover:bg-nt-blue focus:outline-none focus:text-white focus:bg-nt-blue"
                :dusk="dusk+'_option'" @click="select(item)"
            >
              <slot name="option" :option="item" :selected="isSelected(item)" />
            </li>
          </template>
          <p v-else-if="!loading && !(allowCreation && searchTerm)" class="w-full text-gray-500 text-center py-2">
            {{ (allowCreation ? 'Type something to add an option': 'No option available') }}.
          </p>
          <li v-if="allowCreation && searchTerm" role="option"
              class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9  cursor-pointer group hover:text-white hover:bg-nt-blue focus:outline-none focus:text-white focus:bg-nt-blue"
              @click="createOption(searchTerm)"
          >
            Create <b class="px-1 bg-gray-300 rounded group-hover:text-black">{{ searchTerm }}</b>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { directive as onClickaway } from 'vue-clickaway'
import TextInput from '../TextInput'
import Fuse from 'fuse.js'
import { themes } from '~/config/form-themes'
import debounce from 'debounce'

export default {
  name: 'VSelect',
  components: { TextInput },
  directives: {
    onClickaway: onClickaway
  },
  props: {
    data: Array,
    value: { default: null },
    label: { type: String, default: null },
    dusk: { type: String, default: null },
    loading: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    hasError: { type: Boolean, default: false },
    remote: { type: Function, default: null },
    searchKeys: { type: Array, default: () => ['name'] },
    optionKey: { type: String, default: 'id' },
    emitKey: { type: String, default: null }, // Key used for emitted value, emit object if null,
    color: { type: String, default: '#3B82F6' },
    placeholder: { type: String, default: null },
    uppercaseLabels: { type: Boolean, default: true },
    theme: { type: Object, default: () => themes.default },
    allowCreation: { type: Boolean, default: false },
  },
  data () {
    return {
      isOpen: false,
      searchTerm: ''
    }
  },
  computed: {
    inputStyle () {
      return {
        '--tw-ring-color': this.color
      }
    },
    debouncedRemote () {
      if (this.remote) {
        return debounce(this.remote, 300)
      }
      return null
    },
    filteredOptions () {
      if (!this.data) return []
      if (!this.searchable || this.remote || this.searchTerm === '') {
        return this.data
      }

      // Fuze search
      const fuzeOptions = {
        keys: this.searchKeys
      }
      const fuse = new Fuse(this.data, fuzeOptions)
      return fuse.search(this.searchTerm).map((res) => {
        return res.item
      })
    },
    isSearchable () {
      return this.searchable || this.remote !== null || this.allowCreation
    }
  },
  watch: {
    'searchTerm': function (val) {
      if (!this.debouncedRemote) return
      if ((this.remote && val) || (val === '' && !this.value) || (val === '' && this.isOpen)) {
        return this.debouncedRemote(val)
      }
    }
  },
  methods: {
    isSelected (value) {
      if (!this.value) return false

      if (this.emitKey && value[this.emitKey]) {
        value = value[this.emitKey]
      }

      if (this.multiple) {
        return this.value.includes(value)
      }
      return this.value === value
    },
    closeDropdown () {
      this.isOpen = false
      this.searchTerm = ''
    },
    openDropdown () {
      this.isOpen = !this.isOpen
    },
    select (value) {
      if (!this.multiple) {
        this.closeDropdown()
      }

      if (this.emitKey) {
        value = value[this.emitKey]
      }

      if (this.multiple) {
        const emitValue = Array.isArray(this.value) ? [...this.value] : []

        // Already in value, remove it
        if (this.isSelected(value)) {
          this.$emit('input', emitValue.filter((item) => {
            if (this.emitKey) {
              return item !== value
            }
            return item[this.optionKey] !== value && item[this.optionKey] !== value[this.optionKey]
          }))
          return
        }

        // Otherwise add value
        emitValue.push(value)
        this.$emit('input', emitValue)
      } else {
        if (this.value === value) {
          this.$emit('input', null)
        } else {
          this.$emit('input', value)
        }
      }
    },
    createOption(newOption) {
      if(newOption){
        let newItem = {
          'name': newOption,
          'value': newOption,
        }
        this.$emit("update-options", newItem)
        this.select(newItem)
      }
    }
  }
}
</script>
