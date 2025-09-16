/**
 * InputLabel tailwind-variants configuration
 * Used for form input labels
 */
export const inputLabelTheme = {
  slots: {
    label: [
      'input-label',
      'text-neutral-700 dark:text-neutral-300 font-semibold'
    ],
    requiredDot: 'text-red-500 required-dot'
  },
  variants: {
    uppercaseLabels: {
      true: {
        label: 'uppercase text-xs'
      },
      false: {
        label: 'text-sm/none'
      }
    }
  },
  defaultVariants: {
    uppercaseLabels: false
  }
}