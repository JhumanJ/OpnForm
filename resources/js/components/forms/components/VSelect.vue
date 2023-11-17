<template>
  <div class="v-select">
    <label v-if="label"
           :class="[theme.SelectInput.label,{'uppercase text-xs': uppercaseLabels, 'text-sm': !uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <small v-if="help && helpPosition=='above_input'" :class="theme.SelectInput.help" class="flex mb-1">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>

    <div class="relative">
      <span class="inline-block w-full rounded-md">
        <button type="button" :dusk="dusk" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label"
                class="cursor-pointer"
                :style="inputStyle" :class="[theme.SelectInput.input,{'py-2': !multiple || loading,'py-1': multiple, '!ring-red-500 !ring-2': hasError, '!cursor-not-allowed !bg-gray-200': disabled}, inputClass]"
                @click="openDropdown"
        >
          <div :class="{'h-6': !multiple, 'min-h-8': multiple && !loading}">
            <transition name="fade" mode="out-in">
              <loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
              <div v-else-if="modelValue" key="modelValue" class="flex" :class="{'min-h-8': multiple}">
                <slot name="selected" :option="modelValue" />
              </div>
              <div v-else key="placeholder">
                <slot name="placeholder">
                  <div class="text-gray-400 dark:text-gray-500 w-full text-left" :class="{'py-1': multiple && !loading}">
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
        </button>
      </span>
      <div v-show="isOpen" :dusk="dusk+'_dropdown' "
           class="absolute mt-1 rounded-md bg-white dark:bg-notion-dark-light shadow-lg z-10"
           :class="dropdownClass"
      >
        <ul tabindex="-1" role="listbox" aria-labelled by="listbox-label" aria-activedescendant="listbox-item-3"
            class="rounded-md text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5 relative"
            :class="{'max-h-42 py-1': !isSearchable,'max-h-48 pb-1': isSearchable}"
        >
          <div v-if="isSearchable" class="px-2 pt-2 sticky top-0 bg-white dark-bg-notion-dark-light z-10">
            <text-input v-model="searchTerm.value" name="search" :color="color" :theme="theme"
                        placeholder="Search..."
            />
          </div>
          <div v-if="loading" class="w-full py-2 flex justify-center">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
          </div>
          <template v-if="filteredOptions.length > 0">
            <li v-for="item in filteredOptions" :key="item[optionKey]" role="option" :style="optionStyle"
                :class="{'px-3 pr-9': multiple, 'px-3': !multiple}"
                class="text-gray-900 cursor-default select-none relative py-2 cursor-pointer group hover:text-white hover-bg-form-color focus:outline-none focus-text-white focus-nt-blue"
                :dusk="dusk+'_option'" @click="select(item)"
            >
              <slot name="option" :option="item" :selected="isSelected(item)" />
            </li>
          </template>
          <p v-else-if="!loading && !(allowCreation && searchTerm)" class="w-full text-gray-500 text-center py-2">
            {{ (allowCreation ? 'Type something to add an option' : 'No option available') }}.
          </p>
          <li v-if="allowCreation && searchTerm" role="option" :style="optionStyle"
              :class="{'px-3 pr-9': multiple, 'px-3': !multiple}"
              class="text-gray-900 cursor-default select-none relative py-2 cursor-pointer group hover:text-white hover-bg-form-color focus:outline-none focus-text-white focus-nt-blue"
              @click="createOption(searchTerm)"
          >
            Create <b class="px-1 bg-gray-300 rounded group-hover-text-black">{{ searchTerm }}</b>
          </li>
        </ul>
      </div>
    </div>

    <small v-if="help && helpPosition=='below_input'" :class="theme.SelectInput.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, defineProps, defineEmits, defineOptions, computed } from 'vue'
import { vOnClickOutside } from '@vueuse/components'
import TextInput from '../TextInput.vue'
import Fuse from 'fuse.js'
import { themes } from '~/config/form-themes.js'
import debounce from 'debounce'


defineOptions({
  name: 'VSelect'
})

const props = defineProps({
  data: Array,
  modelValue: { default: null },
  inputClass: { type: String, default: null },
  dropdownClass: { type: String, default: 'w-full' },
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
  emitKey: { type: String, default: null },
  color: { type: String, default: '#3B82F6' },
  placeholder: { type: String, default: null },
  uppercaseLabels: { type: Boolean, default: true },
  theme: { type: Object, default: () => themes.default },
  allowCreation: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  help: { type: String, default: null },
  helpPosition: { type: String, default: 'below_input' }
})

const emit = defineEmits(['update:modelValue', 'update-options'])

const defaultValue = ref(props.modelValue)
let searchTerm = ref('')
let isOpen = ref(false)

const optionStyle = computed(() => {
  return {
    '--bg-form-color': props.color
  }
})

const inputStyle = computed(() => {
  return {
    '--tw-ring-color': props.color
  }
})

const debouncedRemote = computed(() => {
  if (props.remote) {
    return debounce(props.remote, 300)
  }
  return null
})

const filteredOptions = computed(() => {
  if (!props.data) return []
  if (!props.searchable || props.remote || searchTerm.value === '') {
    return props.data
  }

  // Fuse search
  const fuzeOptions = {
    keys: props.searchKeys
  }
  const fuse = new Fuse(props.data, fuzeOptions)
  return fuse.search(searchTerm.value).map((res) => {
    return res.item
  })
})

const isSearchable = computed(() => {
  return props.searchable || props.remote !== null || props.allowCreation
})

watch(() => searchTerm, val => {
  if (!props.debouncedRemote) return
  if ((props.remote && val) || (val === '' && !props.modelValue) || (val === '' && isOpen)) {
    return props.debouncedRemote(val)
  }
})


const isSelected = (value) => {
  if (!props.modelValue) return false

  if (props.emitKey && value[props.emitKey]) {
    value = value[props.emitKey]
  }

  if (props.multiple) {
    return props.modelValue.includes(value)
  }
  return props.modelValue === value
}

const closeDropdown = () => {
  isOpen.value = false
  searchTerm.value = ''
}
const openDropdown = () => {
  isOpen.value = props.disabled ? false : !isOpen.value
}
const select = (value) => {
  if (!props.multiple) {
    closeDropdown()
  }

  if (props.emitKey) {
    value = value[props.emitKey]
  }

  if (props.multiple) {
    const emitValue = Array.isArray(props.modelValue) ? [...props.modelValue] : []

    if (isSelected(value)) {
      emit('update:modelValue', emitValue.filter((item) => {
        if (props.emitKey) {
          return item !== value
        }
        return item[props.optionKey] !== value && item[props.optionKey] !== value[props.optionKey]
      }))
      return
    }

    emitValue.push(value)
    emit('update:modelValue', emitValue)
  } else {
    if (props.modelValue === value) {
      emit('update:modelValue', defaultValue ?? null)
    } else {
      emit('update:modelValue', value)
    }
  }
}
const createOption = (newOption) => {
  if (newOption) {
    const newItem = {
      name: newOption,
      value: newOption
    }
    emit('update-options', newItem)
    select(newItem)
  }
}


// export default {
//   components: { TextInput },
//   directives: {
//     onClickaway: vOnClickOutside
//   }
// }
</script>
