/**
 * ScaleInput tailwind-variants configuration
 */
export const scaleInputTheme = {
  slots: {
    button: [
      'cursor-pointer inline-block grow text-center border',
      'text-neutral-700 dark:text-neutral-300'
    ],
    buttonUnselected: [
      'bg-white dark:bg-notion-dark-light'
    ]
  },
  variants: {
    theme: {
      default: {
        button: [
          'border-neutral-300 dark:border-neutral-600'
        ],
        buttonUnselected: [
          'bg-white dark:bg-notion-dark-light'
        ]
      },
      notion: {
        button: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'text-neutral-900 dark:text-neutral-100'
        ],
        buttonUnselected: [
          'bg-notion-input-background dark:bg-notion-dark-light'
        ]
      }
    },
    size: {
      xs: { button: 'py-1 text-xs' },
      sm: { button: 'py-1.5 text-sm' },
      md: { button: 'py-2 text-base' },
      lg: { button: 'py-3 text-lg' }
    },
    borderRadius: {
      none: { button: 'rounded-none' },
      small: { button: 'rounded-lg' },
      full: { button: 'rounded-[20px]' }
    },
    hasError: {
      true: { 
        button: '!border-red-500'
      }
    },
    disabled: {
      true: { 
        button: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800',
        buttonUnselected: '!bg-neutral-200 dark:!bg-neutral-800'
      }
    }
  },
  defaultVariants: {
    theme: 'default',
    size: 'md',
    borderRadius: 'small',
    hasError: false,
    disabled: false
  }
}
