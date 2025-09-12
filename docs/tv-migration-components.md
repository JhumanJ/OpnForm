## Tailwind-Variants Migration - Component Worklist

### Files to CREATE (themes)
- `client/lib/forms/themes/text-area-input.theme.js`
- `client/lib/forms/themes/checkbox-input.theme.js`
- `client/lib/forms/themes/color-input.theme.js`
- `client/lib/forms/themes/slider-input.theme.js`
- `client/lib/forms/themes/toggle-switch-input.theme.js`
- `client/lib/forms/themes/select-input.theme.js`
- `client/lib/forms/themes/flat-select-input.theme.js`
- `client/lib/forms/themes/scale-input.theme.js`

### Files to UPDATE (core)
- `client/components/forms/core/TextAreaInput.vue`
- `client/components/forms/core/CheckboxInput.vue`
- `client/components/forms/core/ColorInput.vue`
- `client/components/forms/core/SliderInput.vue`
- `client/components/forms/core/ToggleSwitchInput.vue`
- `client/components/forms/core/SelectInput.vue`
- `client/components/forms/core/FlatSelectInput.vue`
- `client/components/forms/core/ScaleInput.vue`

### Files to UPDATE (heavy)
- `client/components/forms/heavy/BarcodeInput.vue`
- `client/components/forms/heavy/CodeInput.client.vue`
- `client/components/forms/heavy/DateInput.vue`
- `client/components/forms/heavy/FileInput.vue`
- `client/components/forms/heavy/ImageInput.vue`
- `client/components/forms/heavy/LogicConfirmationModal.vue`
- `client/components/forms/heavy/MatrixInput.vue`
- `client/components/forms/heavy/MentionInput.vue`
- `client/components/forms/heavy/PaymentInput.client.vue`
- `client/components/forms/heavy/PhoneInput.vue`
- `client/components/forms/heavy/RatingInput.vue`
- `client/components/forms/heavy/RichTextAreaInput.client.vue`
- `client/components/forms/heavy/SignatureInput.vue`

### Files to UPDATE (heavy/components)
- `client/components/forms/heavy/components/CameraUpload.vue`
- `client/components/forms/heavy/components/CaptchaInput.vue`
- `client/components/forms/heavy/components/CaptchaWrapper.vue`
- `client/components/forms/heavy/components/HCaptchaV2.vue`
- `client/components/forms/heavy/components/MentionDropdown.vue`
- `client/components/forms/heavy/components/QuillyEditor.vue`
- `client/components/forms/heavy/components/RecaptchaV2.vue`
- `client/components/forms/heavy/components/UploadedFile.vue`

Notes:
- Use one computed `variantSlots` per component and call `variantSlots.slot()` directly in templates.
- Replace legacy `theme.*` usages with TV-driven slots and remove `:theme` props.
- Keep UI overrides via `:ui` prop supported.

