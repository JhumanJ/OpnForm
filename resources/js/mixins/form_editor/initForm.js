import Form from "vform";

export default {
  methods: {
    initForm() {
      this.form = new Form({
        title: 'My Form',
        description: null,
        visibility: 'public',
        workspace_id: this.workspace?.id,
        properties: [],

        notifies: false,
        slack_notifies: false,
        send_submission_confirmation: false,
        webhook_url: null,
        notification_settings: {},

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
        notification_sender: 'OpnForm',
        notification_subject: 'We saved your answers',
        notification_body: 'Hello there 👋 <br>This is a confirmation that your submission was successfully saved.',
        notifications_include_submission: true,
        use_captcha: false,
        is_rating: false,
        rating_max_value: 5,
        max_submissions_count: null,
        max_submissions_reached_text: 'This form has now reached the maximum number of allowed submissions and is now closed.',
        editable_submissions_button_text: 'Edit submission',
        confetti_on_submission: false,

        // Security & Privacy
        can_be_indexed: true,

        // Custom SEO
        seo_meta: {}
      })
    },
  }
}
