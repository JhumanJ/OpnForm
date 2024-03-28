import {generateUUID} from "~/lib/utils.js";

export const initForm = (defaultValue = {}, withDefaultProperties = false) => {
  return useForm({
    title: 'My Form',
    description: null,
    visibility: 'public',
    workspace_id: null,
    properties: withDefaultProperties ? getDefaultProperties() :[],

    // Customization
    theme: 'default',
    width: 'centered',
    dark_mode: 'auto',
    color: '#3B82F6',
    hide_title: false,
    no_branding: false,
    uppercase_labels: true,
    transparent_background: false,
    closes_at: null,
    closed_text: 'This form has now been closed by its owner and does not accept submissions anymore.',
    auto_save: true,

    // Submission
    submit_button_text: 'Submit',
    re_fillable: false,
    re_fill_button_text: 'Fill Again',
    submitted_text: 'Amazing, we saved your answers. Thank you for your time and have a great day!',
    use_captcha: false,
    max_submissions_count: null,
    max_submissions_reached_text: 'This form has now reached the maximum number of allowed submissions and is now closed.',
    editable_submissions_button_text: 'Edit submission',
    confetti_on_submission: false,

    // Security & Privacy
    can_be_indexed: true,

    // Custom SEO
    seo_meta: {},

    ...defaultValue
  })
}

function getDefaultProperties () {
  return [
    {
      name: 'Name',
      type: 'text',
      hidden: false,
      required: true,
      id: generateUUID()
    },
    {
      name: 'Email',
      type: 'email',
      hidden: false,
      id: generateUUID()
    },
    {
      name: 'Message',
      type: 'text',
      hidden: false,
      multi_lines: true,
      id: generateUUID()
    }
  ]
}
