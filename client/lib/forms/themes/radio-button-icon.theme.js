/**
 * RadioButtonIcon tailwind-variants configuration
 * Used for radio button icons in FlatSelectInput and similar components
 */
export const radioButtonIconTheme = {
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
        checkedIcon: 'w-3 h-3',
        uncheckedIcon: 'w-3 h-3'
      },
      sm: {
        checkedIcon: 'w-4 h-4',
        uncheckedIcon: 'w-4 h-4'
      },
      md: {
        checkedIcon: 'w-5 h-5',
        uncheckedIcon: 'w-5 h-5'
      },
      lg: {
        checkedIcon: 'w-6 h-6 mx-1',
        uncheckedIcon: 'w-6 h-6 mx-1'
      }
    }
  },
  defaultVariants: {
    size: 'md'
  }
}