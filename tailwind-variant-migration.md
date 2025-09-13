## Tailwind-Variants Migration - Component Checklist

### Core Input Components

#### Main Components

-   [x] **TextInput.vue** - ✅ COMPLETED
-   [ ] **TextAreaInput.vue** - Pending
-   [ ] **ToggleSwitchInput.vue** - Pending
-   [ ] **SelectInput.vue** - Pending
-   [ ] **SliderInput.vue** - Pending
-   [ ] **FlatSelectInput.vue** - Pending
-   [ ] **ScaleInput.vue** - Pending
-   [ ] **ColorInput.vue** - Pending
-   [ ] **CheckboxInput.vue** - Pending
-   [ ] **VForm.vue** - Provides theme context only, may not need migration

#### Sub-Components (components/ directory)

-   [x] **CheckboxIcon.vue** - ✅ COMPLETED
-   [x] **InputLabel.vue** - ✅ COMPLETED
-   [x] **InputWrapper.vue** - ✅ COMPLETED
-   [x] **RadioButtonIcon.vue** - ✅ COMPLETED
-   [x] **VCheckbox.vue** - ✅ COMPLETED
-   [x] **VSelect.vue** - ✅ COMPLETED

#### Components Not Requiring Migration

-   **TextBlock.vue** - Uses utility classes only
-   **OptionSelectorInput.vue** - Uses utility classes only

### Heavy Sub-Components (components/ directory)

-   [ ] **CameraUpload.vue** - Pending
-   [ ] **CaptchaInput.vue** - Pending
-   [ ] **CaptchaWrapper.vue** - Pending
-   [ ] **HCaptchaV2.vue** - Pending
-   [ ] **MentionDropdown.vue** - Pending
-   [ ] **QuillyEditor.vue** - Pending
-   [ ] **RecaptchaV2.vue** - Pending
-   [ ] **UploadedFile.vue** - Pending

### Heavy Input Components

-   [ ] **DateInput.vue** - Pending
-   [ ] **RichTextAreaInput.client.vue** - Pending
-   [ ] **FileInput.vue** - Pending
-   [ ] **SignatureInput.vue** - Pending
-   [ ] **PaymentInput.client.vue** - Pending
-   [ ] **CodeInput.client.vue** - Pending
-   [ ] **PhoneInput.vue** - Pending
-   [ ] **RatingInput.vue** - Pending
-   [ ] **MatrixInput.vue** - Pending
-   [ ] **MentionInput.vue** - Pending
-   [ ] **ImageInput.vue** - Pending
-   [ ] **LogicConfirmationModal.vue** - May not need migration (modal component)
-   [ ] **BarcodeInput.vue** - Pending

### Migration Guidelines

#### 1. Required Import Changes

```javascript
// Add these imports to every component
import { computed } from "vue";
import { tv } from "tailwind-variants";
import { componentNameTheme } from "~/lib/forms/themes/component-name.theme.js";
```

#### 2. Setup Function Pattern

**Before:**

```javascript
setup(props, context) {
  return {
    ...useFormInput(props, context),
  }
}
```

**After:**

```javascript
setup(props, context) {
  const formInput = useFormInput(props, context)
  const componentVariants = computed(() => tv(componentNameTheme, props.ui))
  const variantSlots = computed(() => componentVariants.value({
    size: formInput.resolvedSize.value,
    borderRadius: formInput.resolvedBorderRadius.value,
    themeName: formInput.resolvedThemeName.value,
    hasError: formInput.hasError.value,
    disabled: props.disabled,
    // ... other variant props as needed
  }))

  return {
    ...formInput,
    variantSlots
  }
}
```

#### 3. Template Class Replacements

**Before:**

```vue
<!-- Complex theme-based classes -->
:class="[ theme.default.input, theme.default.borderRadius,
theme.default.spacing.horizontal, theme.default.fontSize, { '!ring-red-500
!ring-2 !border-transparent': hasError, '!cursor-not-allowed !bg-neutral-200
dark:!bg-neutral-800': disabled, }, ]"

<!-- Simple theme references -->
:class="theme.SelectInput.fontSize" :class="theme.default.help"
```

**After:**

```vue
<!-- Replace with variant slots -->
:class="variantSlots.input()" :class="variantSlots.label()"
:class="variantSlots.help()"
```

**Important:** Use computed properties when variant slots require operations:

```vue
<!-- Good: Direct variant slot usage -->
:class="variantSlots.input()"

<!-- Good: Computed property for operations -->
:class="wrapperClasses"

<script>
// When operations like twMerge are needed
const wrapperClasses = computed(() =>
    twMerge(variantSlots.value.wrapper(), props.customClass)
);
</script>
```

#### 4. Remove Theme Props

```vue
<!-- Remove theme prop from child components -->
<!-- Before: -->
:theme="theme"

<!-- After: Remove completely -->
```

#### 5. Theme File Structure

Create theme files in `~/lib/forms/themes/component-name.theme.js`:

```javascript
export const componentNameTheme = {
    slots: {
        input: "base-input-classes",
        label: "",
        help: "text-neutral-500",
    },
    variants: {
        size: {
            xs: { input: "text-xs px-2 py-1", label: "text-xs" },
            sm: { input: "text-sm px-3 py-2", label: "text-sm" },
            md: { input: "text-base px-4 py-3", label: "text-base" },
            lg: { input: "text-lg px-5 py-4", label: "text-lg" },
        },
        borderRadius: {
            none: { input: "rounded-none" },
            small: { input: "rounded-lg" },
            full: { input: "rounded-[20px]" },
        },
        themeName: {
            default: {
                input: "bg-white dark:bg-notion-dark border-neutral-300 dark:border-neutral-600",
            },
        },
        hasError: {
            true: { input: "!ring-red-500 !ring-2 !border-transparent" },
        },
        disabled: {
            true: {
                input: "!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800",
            },
        },
    },
    defaultVariants: {
        size: "md",
        borderRadius: "small",
    },
};
```

#### 6. Migration Steps Checklist

1. **Create theme file** with appropriate slots and variants
2. **Add required imports** (computed, tv, theme)
3. **Update setup function** to create variantSlots computed
4. **Replace all theme-based classes** with variantSlots calls
5. **Remove theme props** from child components
6. **Test all size/state combinations** to ensure visual consistency
7. **Verify UI prop overrides** still work correctly

#### 7. Common Variant Props

Include these variant dimensions based on component needs:

-   `size`: xs, sm, md, lg
-   `borderRadius`: none, small, full
-   `themeName`: default (and others as needed)
-   `hasError`: boolean for error states
-   `disabled`: boolean for disabled states
-   `focused`: boolean for focus states (where applicable)

#### 8. Key Benefits

-   **Type safety** with proper TypeScript inference
-   **Better performance** with computed variants
-   **Maintainable styling** with centralized theme definitions
-   **Runtime customization** via UI prop overrides

### Components Not Requiring Migration

These core components don't use legacy themes and are already using utility classes:

-   `client/components/forms/core/OptionSelectorInput.vue`
-   `client/components/forms/core/TextBlock.vue`

### Migration Progress

**Core Components:** 6/17 completed (35%)

-   Sub-components: 6/6 ✅ COMPLETED
-   Main components: 1/9 completed (11%)

**Heavy Components:** 0/21 completed (0%)

-   Sub-components: 0/8 completed
-   Main components: 0/13 completed

**Overall Progress:** 7/38 components completed (18%)
