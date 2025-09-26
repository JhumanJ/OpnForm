/**
 * InputLabel tailwind-variants configuration
 * Used for form input labels
 */
export const inputLabelTheme = {
  slots: {
    label: [
      'input-label',
      'text-neutral-700 dark:text-neutral-300'
    ],
    requiredDot: 'text-red-500 required-dot'
  },
  variants: {
    theme: {
      default: {
        label: 'font-semibold'
      },
      simple: {
        label: 'font-semibold'
      },
      notion: {
        label: 'font-semibold'
      },
      minimal: {
        label: 'font-medium text-neutral-600 dark:text-neutral-400'
      }
    },
    size: {
      xs: {
        label: 'text-xs'
      },
      sm: {
        label: 'text-xs'
      },
      md: {
        label: 'text-sm'
      },
      lg: {
        label: 'text-base'
      }
    },
    uppercaseLabels: {
      true: {
        label: 'uppercase'
      },
      false: {
        label: 'leading-none'
      }
    }
  },
  defaultVariants: {
    theme: 'default',
    size: 'md',
    uppercaseLabels: false
  }
}