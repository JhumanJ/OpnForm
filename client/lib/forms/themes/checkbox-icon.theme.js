/**
 * CheckboxIcon tailwind-variants configuration
 * Used for checkbox icons in FlatSelectInput and similar components
 */
export const checkboxIconTheme = {
  slots: {
    checkedIcon: [
      'block',
      'bg-[var(--form-color,#3B82F6)]'
    ],
    uncheckedIcon: [
      'block',
      'text-neutral-300 dark:text-neutral-600'
    ]
  },
  variants: {
    size: {
      xs: {
        checkedIcon: 'size-3',
        uncheckedIcon: 'size-3'
      },
      sm: {
        checkedIcon: 'size-4',
        uncheckedIcon: 'size-4'
      },
      md: {
        checkedIcon: 'size-5',
        uncheckedIcon: 'size-5'
      },
      lg: {
        checkedIcon: 'size-6 mx-1',
        uncheckedIcon: 'size-6 mx-1'
      }
    }
  },
  defaultVariants: {
    size: 'md'
  }
}