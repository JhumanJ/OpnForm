<template>
  <div class="grid grid-cols-3 gap-2">
    <button
      v-for="option in availableOptions"
      :key="option.name"
      class="flex flex-col items-center justify-center p-1.5 border rounded-lg transition-colors text-gray-500"
      :class="[
        option.class ? (typeof option.class === 'function' ? option.class(isSelected(option.name)) : option.class) : {},
        {
          'border-blue-500 bg-blue-50': isSelected(option.name),
          'hover:bg-gray-100 border-gray-300': !isSelected(option.name)
        }
      ]"
      @click="toggleOption(option.name)"
    >
      <Icon
        :name="isSelected(option.name) && option.selectedIcon ? option.selectedIcon : option.icon"
        :class="[
          'w-4 h-4 mb-1',
          {
            'text-blue-500': isSelected(option.name),
            'text-inherit': !isSelected(option.name),
          },
          option.iconClass ? (typeof option.iconClass === 'function' ? option.iconClass(isSelected(option.name)) : option.iconClass) : {}
        ]"
      />
      <span
        class="text-xs" 
        :class="{
          'text-blue-500': isSelected(option.name),
          'text-inherit': !isSelected(option.name),
        }"
      >{{ isSelected(option.name) ? option.selectedLabel ?? option.label : option.label }}</span>
    </button>
  </div>
</template>
  
<script setup>

const props = defineProps({
    field: {
        type: Object,
        required: true
    },
    canBeDisabled: {
        type: Boolean,
        default: true
    },
    canBeRequired: {
        type: Boolean,
        default: true
    },
    canBeHidden: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:field'])

const options = ref([
  { 
    name: 'required', 
    label: 'Required', 
    icon: 'ph:asterisk-bold',
    selectedIcon: 'ph:asterisk-bold',
    iconClass: (isActive) => isActive ? 'text-red-500' : '',
  },
  { 
    name: 'hidden', 
    label: 'Hidden', 
    icon: 'heroicons:eye',
    selectedIcon: 'heroicons:eye-slash-solid',
  },
  { 
    name: 'disabled', 
    label: 'Disabled', 
    icon: 'heroicons:lock-open',
    selectedIcon: 'heroicons:lock-closed-solid',
  }
])

const availableOptions = computed(() => {
  return options.value.filter(option => {
    if (option.name === 'disabled') return props.canBeDisabled
    if (option.name === 'required') return props.canBeRequired
    if (option.name === 'hidden') return props.canBeHidden
    return true
  })
})

const isSelected = (optionName) => {
  return props.field[optionName]
}

const toggleOption = (optionName) => {
  const newValue = !props.field[optionName]

  if (optionName === 'required' && newValue) {
    props.field.hidden = false
  } else if (optionName === 'hidden' && newValue) {
    props.field.required = false
    props.field.disabled = false
    props.field.generates_uuid = false
    props.field.generates_auto_increment_id = false
  } else if (optionName === 'disabled' && newValue) {
    props.field.hidden = false
  }

  if ((optionName === 'disabled' && props.canBeDisabled) ||
      (optionName === 'required' && props.canBeRequired) ||
      (optionName === 'hidden' && props.canBeHidden)) {
    props.field[optionName] = newValue
  }
}
</script>