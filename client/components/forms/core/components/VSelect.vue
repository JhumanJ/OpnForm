<template>
  <div
    ref="select"
    :class="variantSlots.container()"
  >
    <UPopover
      v-model:open="isOpen"
      :content="{
        align: 'start',
        side: 'bottom',
        sideOffset: 4
      }"
      :ui="{
        content: 'w-(--reka-popper-anchor-width) bg-white dark:!bg-notion-dark-light shadow-xl z-30 overflow-auto ' + borderRadiusClass
      }"
    >
      <template #anchor>
        <div
          :style="inputStyle"
          :class="[variantSlots.anchor(), inputClass]"
        >
        <button
          type="button"
          aria-haspopup="listbox"
          :aria-expanded="isOpen"
          aria-labelledby="listbox-label"
          :class="variantSlots.button()"
          @click.stop="toggleDropdown"
          @focus="onFocus"
          @blur="onBlur"
        >
          <div
            :class="variantSlots.buttonInner()"
          >
            <transition
              name="fade"
              mode="out-in"
            >
              <Loader
                v-if="loading"
                key="loader"
                class="h-6 w-6 text-form mx-auto"
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
                    :class="[
                      variantSlots.placeholder(),
                      { 'py-1': multiple && !loading }
                    ]"
                  >
                    {{ placeholder }}
                  </div>
                </slot>
              </div>
            </transition>
          </div>
          <div
            :class="variantSlots.chevronGradient()"
          />
          <span
            :class="variantSlots.chevronContainer()"
          >
            <Icon
              name="heroicons:chevron-up-down-16-solid" 
              :class="variantSlots.chevronIcon()"
            />
          </span>
        </button>
        <button
          v-if="clearable && showClearButton && !disabled && !isEmpty"
          :class="variantSlots.clearButton()"
          @click.stop.prevent="clear()"
        >
          <Icon
            name="heroicons:x-mark-20-solid"
            :class="variantSlots.clearIcon()"
            width="2em"
            dynamic
          />
        </button>
        </div>
      </template>

      <template #content>
        <ul
          tabindex="-1"
          role="listbox"
          :class="variantSlots.dropdown()"
        >
          <div
            v-if="isSearchable"
            :class="variantSlots.searchContainer()"
          >
            <input
              v-model="searchTerm"
              type="text"
              :class="variantSlots.searchInput()"
              :placeholder="allowCreation ? $t('forms.select.searchOrTypeToCreateNew') : $t('forms.select.search')"
            >
            <div
              v-if="!searchTerm"
              :class="variantSlots.searchIconContainer()"
            >
              <Icon
                name="heroicons:magnifying-glass-solid"
                :class="variantSlots.searchIcon()"
              />
            </div>
            <div
              v-else
              role="button"
              :class="variantSlots.searchClearContainer()"
              @click.stop="searchTerm = ''"
            >
              <Icon
                name="heroicons:backspace"
                :class="variantSlots.searchClearIcon()"
              />
            </div>
          </div>
          <div
            v-if="loading"
            class="w-full py-2 flex justify-center"
          >
            <Loader class="h-6 w-6 text-blue-500 mx-auto" />
          </div>
          <div
            v-if="filteredOptions.length > 0"
            ref="dropdownRef"
            :class="variantSlots.optionsContainer()"
             
          >
            <li
              v-if="virtualizer"
              v-for="virtualItem in virtualizer.getVirtualItems()"
              :key="filteredOptions[virtualItem.index] ? filteredOptions[virtualItem.index][optionKey] : virtualItem.index"
              role="option"
              :style="optionStyle"
              :class="[
                variantSlots.option(),
                dropdownClass,
                { 'pr-9': multiple},
                { 
                  'opacity-50 cursor-not-allowed': filteredOptions[virtualItem.index] && disabledOptionsMap[filteredOptions[virtualItem.index][optionKey]],
                  'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900': filteredOptions[virtualItem.index] && !disabledOptionsMap[filteredOptions[virtualItem.index][optionKey]]
                }
              ]"
              @click.stop="filteredOptions[virtualItem.index] && select(filteredOptions[virtualItem.index])"
            >
              <slot
                v-if="filteredOptions[virtualItem.index]"
                name="option"
                :option="filteredOptions[virtualItem.index]"
                :selected="isSelected(filteredOptions[virtualItem.index])"
              />
            </li>
          </div>
          <slot
            v-else-if="!loading && !(allowCreation && searchTerm)"
            name="empty-placeholder"
          >
            <p
              :class="variantSlots.emptyMessage()"
            >
              {{ (allowCreation ? $t('forms.select.typeSomethingToAddAnOption') : $t('forms.select.noOptionAvailable')) }}.
            </p>
          </slot>
          <div
            v-if="allowCreation && searchTerm"
            class="border-t border-neutral-300 p-1"
          >
            <li
              role="option"
              :style="optionStyle"
              :class="[
                variantSlots.createOption(),
                { 'px-3 pr-9': multiple, 'px-3': !multiple },
                dropdownClass
              ]"
              @click.stop="createOption(searchTerm)"
            >
                            {{ $t('forms.select.create') }} <span :class="variantSlots.createLabel()">{{
                searchTerm
              }}</span>
            </li>
          </div>
          <slot name="after-options" />
        </ul>
      </template>
    </UPopover>
  </div>
</template>

<script>
import debounce from 'debounce'
import Fuse from 'fuse.js'
import { tv } from "tailwind-variants"
import { vSelectTheme } from "~/lib/forms/themes/v-select.theme.js"
import { useVirtualizer } from '@tanstack/vue-virtual'

export default {
  name: 'VSelect',
  components: {},
  directives: {},
  props: {
    data: Array,
    modelValue: { default: null, type: [String, Number, Array, Object, Boolean] },
    inputClass: { type: String, default: null },
    dropdownClass: { type: String, default: 'w-full' },
    loading: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    clearable: { type: Boolean, default: false },
    hasError: { type: Boolean, default: false },
    remote: { type: Function, default: null },
    searchKeys: { type: Array, default: () => ['name'] },
    optionKey: { type: String, default: 'id' },
    emitKey: { type: String, default: null },
    color: { type: String, default: '#3B82F6' },
    placeholder: { type: String, default: null },
    uppercaseLabels: { type: Boolean, default: true },
    showClearButton: { type: Boolean, default: true },
    // Theme configuration as strings for tailwind-variants
    theme: {type: String, default: null},
    size: {type: String, default: null}, 
    borderRadius: {type: String, default: null},
    ui: {type: Object, default: () => ({})},
    allowCreation: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    minSelection: { type: Number, default: null },
      maxSelection: { type: Number, default: null },
      // Local search performance tuning
      fuseOptions: { type: Object, default: () => ({}) },
      searchDebounceMs: { type: Number, default: 150 },
      minSearchLength: { type: Number, default: 1 }
  },
  emits: ['update:modelValue', 'update-options', 'focus', 'blur'],
  data () {
    return {
      isOpen: false,
      searchTerm: '',
        debouncedTerm: '',
      defaultValue: this.modelValue ?? null,
      isFocused: false,
        virtualizer: null,
        fuse: null,
        fuseIndex: null,
        updateDebouncedTerm: null
    }
  },
  computed: {
    // Resolve theme values with proper reactivity
    resolvedTheme() {
      return this.theme || 'default'
    },
    resolvedSize() {
      return this.size || 'md'
    },
    resolvedBorderRadius() {
      return this.borderRadius || 'small'
    },

    // Create select variants with UI prop merging
    vSelectVariants() {
      return tv(vSelectTheme, this.ui)
    },

    // Single variant computation
    variantSlots() {
      return this.vSelectVariants({
        theme: this.resolvedTheme,
        size: this.resolvedSize,
        borderRadius: this.resolvedBorderRadius,
        hasError: this.hasError,
        disabled: this.disabled,
        focused: this.isFocused,
        multiple: this.multiple,
        searchable: this.isSearchable
      })
    },

    // Use variantSlots directly in the template for classes
    borderRadiusClass() {
      return this.variantSlots.anchor().match(/rounded-\S+/)?.[0] || 'rounded-lg'
    },

    optionStyle () {
      return {
        '--bg-form-color': this.color
      }
    },
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

      // If not searchable, remote search is used, or term too short/empty, return raw data
      if (!this.searchable || this.remote) {
        return this.data
      }

      const term = this.debouncedTerm
      if (!term || term.length < this.minSearchLength) {
        return this.data
      }

      // Ensure Fuse is ready
      if (!this.fuse) {
        this.buildFuse()
      }
      if (!this.fuse) return this.data

      return this.fuse.search(term).map((res) => res.item)
    },
    isSearchable () {
      return this.searchable || this.remote !== null || this.allowCreation
    },
    isEmpty () {
      return this.multiple ? !this.modelValue || this.modelValue.length === 0 : !this.modelValue
    },
    selectedCount () {
      if (!this.multiple || !Array.isArray(this.modelValue)) return 0
      return this.modelValue.length
    },
    maxSelectionReached () {
      if (!this.multiple || !this.maxSelection) return false
      return this.selectedCount >= this.maxSelection
    },
    disabledOptionsMap () {
      if (!this.multiple || !this.maxSelection || !this.data) return {}
      
      const map = {}
      for (const item of this.data) {
        const isSelected = this.isSelected(item)
        map[item[this.optionKey]] = !isSelected && this.maxSelectionReached
      }
      return map
    }
  },
  watch: {
    searchTerm (val) {
      // Remote search path
      if (this.debouncedRemote) {
        if ((this.remote && val) || (val === '' && !this.modelValue) || (val === '' && this.isOpen)) {
          this.debouncedRemote(val)
        }
      } else {
        // Local search path: debounce updates
        if (this.updateDebouncedTerm) this.updateDebouncedTerm(val)
      }
    },
    data () {
      // Rebuild fuse index when options change
      this.buildFuse()
    },
    isOpen (val) {
      if (val) {
        this.$nextTick(() => {
          this.setupVirtualizer()
        })
      }
    },
    filteredOptions () {
      if (this.isOpen) {
        this.$nextTick(() => {
          this.setupVirtualizer()
        })
      }
    }
  },
  mounted () {
    // Initialize fuse for local search and debounce handler
    this.buildFuse()
    this.updateDebouncedTerm = debounce((val) => {
      this.debouncedTerm = val
    }, this.searchDebounceMs)
  },
  beforeUnmount () {
    // Clean up virtualizer if it exists
    if (this.virtualizer) {
      this.virtualizer = null
    }
    if (this.updateDebouncedTerm && this.updateDebouncedTerm.clear) {
      this.updateDebouncedTerm.clear()
    }
  },
  methods: {
    buildFuse () {
      if (!this.data || !Array.isArray(this.data) || this.data.length === 0) {
        this.fuse = null
        this.fuseIndex = null
        return
      }

      const options = Object.assign({
        keys: this.searchKeys,
        threshold: 0.3,
        ignoreLocation: true,
        includeScore: false
      }, this.fuseOptions || {})

      try {
        const index = Fuse.createIndex(options.keys, this.data)
        this.fuseIndex = index
        this.fuse = new Fuse(this.data, options, index)
      } catch {
        // Fallback without precomputed index
        this.fuse = new Fuse(this.data, options)
        this.fuseIndex = null
      }
    },
    setupVirtualizer () {
      if (!this.$refs.dropdownRef || !this.filteredOptions || this.filteredOptions.length === 0) {
        this.virtualizer = null
        return
      }
      
      // Clean up existing virtualizer
      if (this.virtualizer) {
        this.virtualizer = null
      }
      
      this.virtualizer = useVirtualizer({
        count: this.filteredOptions.length,
        getScrollElement: () => this.$refs.dropdownRef,
        estimateSize: () => 40,
        overscan: 5
      })
    },
    isSelected (value) {
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
      if (this.disabled) return
      this.isFocused = true
      this.$emit('focus', event)
    },

    onBlur(event) {
      if (this.disabled) return
      this.isFocused = false
      this.$emit('blur', event)
    },

    toggleDropdown () {
      if (this.disabled) {
        this.isOpen = false
        return
      }
      
      this.isOpen = !this.isOpen
      
      if (!this.isOpen) {
        this.searchTerm = ''
      }
    },
    select (value) {
      // Check if option is disabled (similar to how we check for input disabled)
      if (this.disabledOptionsMap[value[this.optionKey]]) {
        return
      }

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
    clear () {
      this.$emit('update:modelValue', this.multiple ? [] : null)
    },
    createOption (newOption) {
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