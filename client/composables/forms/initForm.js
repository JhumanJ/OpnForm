import clonedeep from 'clone-deep'
import { generateUUID } from "~/lib/utils.js"
export const DEFAULT_COLOR = '#3B82F6'

export const initForm = (defaultValue = {}, withDefaultProperties = false) => {
  return useForm({
    title: "My Form",
    visibility: "public",
    workspace_id: null,
    properties: withDefaultProperties ? getDefaultProperties() : [],

    // Customization
    language: 'en',
    font_family: null,
    theme: "default",
    width: "centered",
    layout_rtl: false,
    dark_mode: "auto",
    color: DEFAULT_COLOR,
    hide_title: false,
    no_branding: false,
    uppercase_labels: false,
    transparent_background: false,
    closes_at: null,
    closed_text:
      "This form has now been closed by its owner and does not accept submissions anymore.",
    auto_save: true,
    auto_focus: true,
    border_radius: 'small',
    size: 'md',

    // Submission
    submit_button_text: "Submit",
    re_fillable: false,
    re_fill_button_text: "Fill Again",
    submitted_text:
      "Amazing, we saved your answers. Thank you for your time and have a great day!",
    use_captcha: false,
    captcha_provider: 'recaptcha',
    max_submissions_count: null,
    max_submissions_reached_text:
      "This form has now reached the maximum number of allowed submissions and is now closed.",
    editable_submissions_button_text: "Edit submission",
    confetti_on_submission: false,

    // Security & Privacy
    can_be_indexed: true,

    // Custom SEO
    seo_meta: {},

    ...defaultValue,
  })
}

function getDefaultProperties() {
  return [
    {
      name: "Name",
      type: "text",
      hidden: false,
      required: true,
      id: generateUUID(),
    },
    {
      name: "Email",
      type: "email",
      hidden: false,
      id: generateUUID(),
    },
    {
      name: "Message",
      type: "text",
      hidden: false,
      multi_lines: true,
      id: generateUUID(),
    },
  ]
}

/**
 * Sets default values for form properties if they are not already defined.
 * This function ensures that all necessary form fields have a valid initial value,
 * which helps maintain consistency and prevents errors due to undefined properties.
 * 
 * @param {Object} formData - The initial form data object
 * @returns {Object} A new object with default values applied where necessary
 */
export function setFormDefaults(formData) {
  const defaultValues = {
    title: 'Untitled Form',
    visibility: 'public',
    theme: 'default',
    width: 'centered',
    size: 'md',
    border_radius: 'small',
    dark_mode: 'light',
    color: '#3B82F6',
    hide_title: false,
    uppercase_labels: false,
    no_branding: false,
    transparent_background: false,
    submit_button_text: 'Submit',
    confetti_on_submission: false,
    show_progress_bar: false,
    bypass_success_page: false,
    can_be_indexed: true,
    use_captcha: false,
    captcha_provider: 'recaptcha',
    properties: [],
  }

  const filledFormData = clonedeep(formData)

  for (const [key, value] of Object.entries(defaultValues)) {
    if (filledFormData[key] === undefined || filledFormData[key] === null || (typeof value === 'string' && filledFormData[key] === '')) {
      filledFormData[key] = value
    }
  }

  // Handle required nested properties
  if (filledFormData.properties && Array.isArray(filledFormData.properties)) {
    filledFormData.properties = filledFormData.properties.map(property => ({
      ...property,
      name: property.name === '' || property.name === null || property.name === undefined ? 'Untitled' : property.name,
    }))
  }

  return filledFormData
}
