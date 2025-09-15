# Component Migration Checklist

## Components to Migrate to New useFormInput Pattern

### ✅ COMPLETED

-   [x] `client/components/forms/core/TextInput.vue` - **DONE** ✅

### 📋 PENDING MIGRATION

#### Core Form Components

-   [ ] `client/components/forms/core/CheckboxInput.vue`
-   [ ] `client/components/forms/core/ColorInput.vue`
-   [ ] `client/components/forms/core/FlatSelectInput.vue`
-   [ ] `client/components/forms/core/OptionSelectorInput.vue`
-   [ ] `client/components/forms/core/ScaleInput.vue`
-   [ ] `client/components/forms/core/SelectInput.vue`
-   [ ] `client/components/forms/core/SliderInput.vue`
-   [ ] `client/components/forms/core/TextAreaInput.vue`
-   [ ] `client/components/forms/core/TextBlock.vue`
-   [ ] `client/components/forms/core/ToggleSwitchInput.vue`
-   [ ] `client/components/forms/core/VForm.vue`

#### Core Component Helpers

-   [ ] `client/components/forms/core/components/CheckboxIcon.vue`
-   [ ] `client/components/forms/core/components/InputLabel.vue`
-   [ ] `client/components/forms/core/components/InputWrapper.vue`
-   [ ] `client/components/forms/core/components/RadioButtonIcon.vue`
-   [ ] `client/components/forms/core/components/VCheckbox.vue`
-   [ ] `client/components/forms/core/components/VSelect.vue`

#### Heavy Form Components

-   [ ] `client/components/forms/heavy/BarcodeInput.vue`
-   [ ] `client/components/forms/heavy/CodeInput.client.vue`
-   [ ] `client/components/forms/heavy/DateInput.vue`
-   [ ] `client/components/forms/heavy/FileInput.vue`
-   [ ] `client/components/forms/heavy/ImageInput.vue`
-   [ ] `client/components/forms/heavy/MatrixInput.vue`
-   [ ] `client/components/forms/heavy/MentionInput.vue`
-   [ ] `client/components/forms/heavy/PaymentInput.client.vue`
-   [ ] `client/components/forms/heavy/PhoneInput.vue`
-   [ ] `client/components/forms/heavy/RatingInput.vue`

**Total Components**: 28 pending + 1 completed = **29 total components**

---

## 🚀 UNIFIED MIGRATION GUIDE

### Step 1: Update Imports

```js
// OLD PATTERN ❌
import { useFormInput } from "../useFormInput.js";
import { tv } from "tailwind-variants";
import { someTheme } from "~/lib/forms/themes/some-theme.js";

// NEW PATTERN ✅
import { useFormInput } from "../useFormInput.js";
import { someTheme } from "~/lib/forms/themes/some-theme.js";
// No need to import tv anymore - it's in useFormInput.js
```

### Step 2: Simplify Setup Function

```js
// OLD PATTERN ❌ (Complex, Multiple Computeds)
setup(props, context) {
  const formInput = useFormInput(props, context)
  const componentVariants = computed(() => tv(someTheme, props.ui))
  const variantSlots = computed(() => {
    return componentVariants.value({
      theme: formInput.resolvedTheme.value,
      size: formInput.resolvedSize.value,
      // ... other variants
    })
  })

  return { ...formInput, variantSlots }
}

// NEW PATTERN ✅ (Simple, Centralized)
setup(props, context) {
  const formInput = useFormInput(props, context, {
    variants: someTheme,                    // Theme config object
    additionalVariants: {                   // Component-specific variants (optional)
      loading: props.loading,
      multiple: props.multiple,
      // ... any component-specific variants
    }
  })

  return { ...formInput }  // ui is included automatically
}
```

### Step 3: Update Template Usage

```vue
<!-- OLD PATTERN ❌ -->
<input :class="variantSlots.input()" />
<label :class="variantSlots.label()" />
<div :class="variantSlots.help()">Help text</div>

<!-- NEW PATTERN ✅ (Same, but ui comes from useFormInput) -->
<input :class="ui.input()" />
<label :class="ui.label()" />
<div :class="ui.help()">Help text</div>
```

### Step 4: Update Theme Files (If Needed)

```js
// Ensure theme files use 'theme' not 'themeName'
export const someTheme = {
    variants: {
        theme: {
            // ✅ Use 'theme'
            default: {
                /* ... */
            },
            notion: {
                /* ... */
            },
        },
        // ... other variants
    },
};
```

---

## 🎯 MIGRATION CHECKLIST PER COMPONENT

For each component, verify:

### ✅ Imports

-   [ ] Remove `tv` import (now in useFormInput.js)
-   [ ] Keep theme import: `import { componentTheme } from "~/lib/forms/themes/component-theme.js"`

### ✅ Setup Function

-   [ ] Pass `variants: componentTheme` to useFormInput options
-   [ ] Add `additionalVariants: {}` if component has specific variants (loading, multiple, etc.)
-   [ ] Remove manual computed properties for variants
-   [ ] Return only `{ ...formInput }` (includes `ui` automatically)

### ✅ Template

-   [ ] Replace `variantSlots.slot()` with `ui.slot()`
-   [ ] Remove any `:theme` prop bindings from child components
-   [ ] Verify all class bindings use `ui.slotName()`

### ✅ Theme File

-   [ ] Ensure variant key is `theme` not `themeName`
-   [ ] Verify defaultVariants uses `theme: 'default'`

---

## 🚦 PRIORITY ORDER

**High Priority (Form Editor Performance)**

1. `SelectInput.vue` - Used heavily in FormEditorSidebar
2. `TextAreaInput.vue` - Common input type
3. `CheckboxInput.vue` - Common input type
4. `VSelect.vue` - Core select component

**Medium Priority** 5. All other `core/` components 6. Component helpers in `core/components/`

**Lower Priority**  
7. `heavy/` components (less frequently used)

---

## 🧪 TESTING APPROACH

For each migrated component:

1. **Visual Test**: Verify styling looks identical
2. **Functional Test**: Verify form submission works
3. **Theme Test**: Switch themes (default → notion → simple)
4. **Editor Test**: Test in FormEditorSidebar for performance
5. **Error State Test**: Verify error styling works

---

## 📈 EXPECTED BENEFITS

-   **50%+ Performance Improvement** in FormEditorSidebar
-   **Elimination of Double Computed** layers causing cascading re-renders
-   **Consistent Pattern** across all form components
-   **Simplified Code** - less boilerplate per component
-   **Better Maintainability** - centralized variant logic
