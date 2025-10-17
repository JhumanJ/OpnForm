/**
 * InputLabel tailwind-variants configuration
 * Used for form input labels
 */
export const inputLabelTheme = {
  slots: {
    label: [
      'input-label inline align-baseline',
      'text-neutral-700 dark:text-neutral-300'
    ],
    requiredDot: 'text-red-500 dark:text-red-400 rounded-full bg-red-100 dark:bg-red-500/20 required-dot inline-flex items-center align-middle ml-1 *:size-4 *:-m-0.5'
  },
  variants: {
    presentation: {
      classic: {},
      focused: {
        label: 'leading-none'
      }
    },
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
        label: 'text-xs',
      },
      md: {
        label: 'text-sm',
        requiredDot: 'mt-[-2px]!'
      },
      lg: {
        label: 'text-base',
        requiredDot: '*:size-5 mt-[-3px]!'
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
  compoundVariants: [
    // Label font size adjustments for focused presentation (increase by 1 step)
    { presentation: 'focused', size: 'xs', class: { label: 'text-sm leading-none' } },
    { presentation: 'focused', size: 'sm', class: { label: 'text-sm leading-none' } },
    { presentation: 'focused', size: 'md', class: { label: 'text-base leading-none' } },
    { presentation: 'focused', size: 'lg', class: { label: 'text-xl leading-none' } }
  ],
  defaultVariants: {
    theme: 'default',
    size: 'md',
    uppercaseLabels: false,
    presentation: 'classic'
  }
}