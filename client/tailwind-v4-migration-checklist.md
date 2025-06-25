# Tailwind CSS v4 Migration Checklist

## Class Replacements Required:

- Shadow utilities: `shadow-sm` → `shadow-xs`, `shadow` → `shadow-sm`
- Drop shadow utilities: `drop-shadow-sm` → `drop-shadow-xs`, `drop-shadow` → `drop-shadow-sm`
- Blur utilities: `blur-sm` → `blur-xs`, `blur` → `blur-sm`
- Backdrop blur utilities: `backdrop-blur-sm` → `backdrop-blur-xs`, `backdrop-blur` → `backdrop-blur-sm`
- Border radius utilities: `rounded-sm` → `rounded-xs`, `rounded` → `rounded-sm`
- Focus utilities: `outline-none` → `outline-hidden`
- Ring utilities: `ring` → `ring-3`

## Files to Update:

### Shadow Classes (shadow-sm → shadow-xs, shadow → shadow-sm)

- [x] pages/index.vue
- [x] pages/templates/[slug].vue
- [x] pages/ai-form-builder.vue
- [x] pages/forms/[slug]/show/stats.vue
- [x] pages/settings/billing.vue
- [x] pages/integrations/index.vue
- [x] pages/forms/[slug]/show/integrations/index.vue
- [x] pages/update-credentials.vue
- [x] components/open/forms/components/form-components/FormEditorPreview.vue
- [x] components/open/forms/components/FormStats.vue
- [x] components/open/forms/OpenFormField.vue
- [x] components/pages/welcome/AiFeature.vue
- [x] components/pages/templates/SingleTemplate.vue
- [x] components/settings/ProviderCard.vue
- [x] components/settings/AccessTokenCard.vue
- [x] components/pages/pricing/MonthlyYearlySelector.vue
- [x] components/pages/admin/AdminCard.vue
- [x] components/open/forms/components/FormUrlPrefill.vue
- [x] components/open/forms/components/FirstSubmissionModal.vue
- [x] components/global/Card.vue
- [x] components/open/integrations/components/IntegrationCard.vue
- [x] components/forms/ImageInput.vue
- [x] components/forms/components/CameraUpload.vue

### Backdrop Blur Classes (backdrop-blur-sm → backdrop-blur-xs, backdrop-blur → backdrop-blur-sm)

- [x] pages/index.vue
- [x] pages/ai-form-builder.vue
- [x] components/global/Modal.vue
- [x] components/forms/components/CameraUpload.vue
- [x] components/open/forms/components/form-components/FormEditorPreview.vue
- [x] components/pages/pricing/SubscriptionModal.vue
- [x] components/open/forms/components/FirstSubmissionModal.vue

### Rounded Classes (rounded-sm → rounded-xs, rounded → rounded-sm)

- [x] lib/forms/themes/form-themes.js
- [x] pages/integrations/index.vue
- [x] components/workspaces/WorkspaceIcon.vue
- [x] components/open/tables/components/OpenFile.vue
- [x] components/open/forms/components/FormUrlPrefill.vue
- [x] components/open/forms/components/FormWorkspaceModal.vue
- [x] components/open/forms/components/FormFieldsEditor.vue
- [x] components/open/forms/components/form-components/FormCustomization.vue
- [x] components/open/forms/fields/components/FieldOptions.vue
- [x] components/global/WorkspaceDropdown.vue
- [x] components/global/EditableTag.vue
- [x] components/forms/PhoneInput.vue
- [x] components/forms/ImageInput.vue
- [x] components/forms/BarcodeInput.vue
- [x] components/forms/components/VCheckbox.vue
- [x] components/forms/components/UploadedFile.vue
- [x] components/forms/components/VSelect.vue
- [x] components/forms/components/CameraUpload.vue
- [x] components/pages/forms/show/EmbedFormAsPopupModal.vue

### Outline Classes (outline-none → outline-hidden)

- [x] components/pages/pricing/SubscriptionModal.vue
- [x] components/pages/pricing/CustomPlan.vue
- [x] components/global/EditableTag.vue
- [x] components/global/VButton.vue
- [x] components/forms/BarcodeInput.vue
- [x] components/forms/validation/AlertSuccess.vue
- [x] components/forms/OptionSelectorInput.vue
- [x] components/forms/ImageInput.vue
- [x] components/forms/FileInput.vue
- [x] components/forms/components/VSelect.vue
- [x] lib/forms/themes/form-themes.js

### Ring Classes (ring → ring-3)

- [x] pages/index.vue
- [x] pages/home.vue
- [x] pages/pricing.vue
- [x] pages/ai-form-builder.vue
- [x] pages/templates/[slug].vue
- [x] components/global/WorkspaceDropdown.vue
- [x] components/global/VButton.vue
- [x] components/global/Dropdown.vue
- [x] components/open/editors/FontCard.vue
- [x] components/open/forms/OpenFormButton.vue
- [x] mixins/forms/input.js

## Progress

- **Total Files**: 62
- **Completed**: 62
- **Remaining**: 0
