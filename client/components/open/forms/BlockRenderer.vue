<template>
  <ClientOnlyWrapper v-if="hasComponent" :client-only="clientOnlyVal">
    <Suspense>
      <component
        :is="componentVal"
        v-bind="boundProps"
      />
      <template #fallback>
        <USkeleton class="w-full h-16 my-1.5" />
      </template>
    </Suspense>
    <template #fallback>
      <USkeleton class="w-full h-16 my-1.5" />
    </template>
  </ClientOnlyWrapper>
</template>

<script setup>
import ClientOnlyWrapper from '~/components/global/ClientOnlyWrapper.vue'
import { useComponentRegistry } from '~/composables/components/useComponentRegistry'

const props = defineProps({
  block: { type: Object, required: true },
  formManager: { type: Object, required: true }
})

const form = computed(() => props.formManager?.config?.value || {})
const dataForm = computed(() => props.formManager?.form || {})
const darkMode = computed(() => props.formManager?.darkMode?.value || false)

const { getFormComponent } = useComponentRegistry()

const componentInfo = computed(() => {
  const field = props.block
  let componentName
  if (field.type === 'text' && field.multi_lines) componentName = 'TextAreaInput'
  else if (field.type === 'url' && field.file_upload) componentName = 'FileInput'
  else if (['select','multi_select'].includes(field.type) && field.without_dropdown) componentName = 'FlatSelectInput'
  else if (field.type === 'checkbox' && field.use_toggle_switch) componentName = 'ToggleSwitchInput'
  else if (field.type === 'signature') componentName = 'SignatureInput'
  else if (field.type === 'phone_number' && !field.use_simple_text_input) componentName = 'PhoneInput'
  else {
    componentName = {
      text: 'TextInput',
      rich_text: 'RichTextAreaInput',
      number: 'TextInput',
      rating: 'RatingInput',
      scale: 'ScaleInput',
      slider: 'SliderInput',
      select: 'SelectInput',
      multi_select: 'SelectInput',
      date: 'DateInput',
      files: 'FileInput',
      checkbox: 'CheckboxInput',
      url: 'TextInput',
      email: 'TextInput',
      phone_number: 'TextInput',
      matrix: 'MatrixInput',
      barcode: 'BarcodeInput',
      payment: 'PaymentInput',
      code: 'CodeInput'
    }[field.type]
  }
  return getFormComponent(componentName)
})

const componentVal = computed(() => componentInfo.value && componentInfo.value.component ? componentInfo.value.component : null)
const clientOnlyVal = computed(() => (componentInfo.value && componentInfo.value.clientOnly) ? componentInfo.value.clientOnly : false)
const hasComponent = computed(() => !!componentVal.value)

const shouldInjectBetweenMedia = computed(() => (props.block?.image && props.block.image.url && (props.block.image.layout === 'between'))) 

const boundProps = computed(() => {
  const field = props.block
  const inputProperties = {
    key: field.id,
    name: field.id,
    form: dataForm.value,
    label: (field.hide_field_name) ? null : field.name + ((field.hidden) ? ' (Hidden Field)' : ''),
    color: form.value.color,
    placeholder: field.placeholder,
    help: field.help,
    helpPosition: (field.help_position) ? field.help_position : 'below_input',
    uppercaseLabels: form.value.uppercase_labels == 1 || form.value.uppercase_labels == true,
    maxCharLimit: (field.max_char_limit) ? parseInt(field.max_char_limit) : null,
    showCharLimit: field.show_char_limit || false,
    isDark: darkMode.value,
    locale: (form.value?.language) ? form.value.language : 'en',
    media: shouldInjectBetweenMedia.value ? field.image : null
  }

  if (field.type === 'matrix') {
    inputProperties.rows = field.rows
    inputProperties.columns = field.columns
  }
  if (field.type === 'barcode') inputProperties.decoders = field.decoders

  if (['select', 'multi_select'].includes(field.type)) {
    inputProperties.options = (field[field.type])
      ? field[field.type].options.map(option => ({ name: option.name, value: option.name }))
      : []
    inputProperties.multiple = (field.type === 'multi_select')
    inputProperties.allowCreation = (field.allow_creation === true)
    inputProperties.searchable = (inputProperties.options.length > 4)
    if (field.type === 'multi_select') {
      inputProperties.minSelection = field.min_selection || null
      inputProperties.maxSelection = field.max_selection || null
    }
  } else if (field.type === 'date') {
    inputProperties.dateFormat = field.date_format
    inputProperties.timeFormat = field.time_format
    if (field.with_time) inputProperties.withTime = true
    if (field.date_range) inputProperties.dateRange = true
    if (field.disable_past_dates) inputProperties.disablePastDates = true
    else if (field.disable_future_dates) inputProperties.disableFutureDates = true
  } else if (field.type === 'files' || (field.type === 'url' && field.file_upload)) {
    inputProperties.multiple = (field.multiple !== undefined && field.multiple)
    inputProperties.cameraUpload = (field.camera_upload !== undefined && field.camera_upload)
    let maxFileSize = (form.value?.workspace && form.value?.workspace.max_file_size) ? form.value?.workspace?.max_file_size : 10
    if (field?.max_file_size > 0) maxFileSize = Math.min(field.max_file_size, maxFileSize)
    inputProperties.mbLimit = maxFileSize
    inputProperties.accept = (form.value.is_pro && field.allowed_file_types) ? field.allowed_file_types : ''
  } else if (field.type === 'rating') {
    inputProperties.numberOfStars = parseInt(field.rating_max_value) ?? 5
  } else if (field.type === 'scale') {
    inputProperties.minScale = parseFloat(field.scale_min_value) ?? 1
    inputProperties.maxScale = parseFloat(field.scale_max_value) ?? 5
    inputProperties.stepScale = parseFloat(field.scale_step_value) ?? 1
  } else if (field.type === 'slider') {
    inputProperties.minSlider = parseInt(field.slider_min_value) ?? 0
    inputProperties.maxSlider = parseInt(field.slider_max_value) ?? 50
    inputProperties.stepSlider = parseInt(field.slider_step_value) ?? 5
  } else if (field.type === 'number' || (field.type === 'phone_number' && field.use_simple_text_input)) {
    inputProperties.pattern = '/d*'
  } else if (field.type === 'phone_number' && !field.use_simple_text_input) {
    inputProperties.unavailableCountries = field.unavailable_countries ?? []
  } else if (field.type === 'text' && field.secret_input) {
    inputProperties.nativeType = 'password'
  } else if (field.type === 'payment') {
    inputProperties.direction = form.value.layout_rtl ? 'rtl' : 'ltr'
    inputProperties.currency = field.currency
    inputProperties.amount = field.amount
    inputProperties.oauthProviderId = field.stripe_account_id
    if (props.formManager?.payment) {
      try { inputProperties.paymentData = props.formManager.payment.getPaymentData(field) } catch (e) { console.error(e) }
    }
  }

  return inputProperties
})
</script>


