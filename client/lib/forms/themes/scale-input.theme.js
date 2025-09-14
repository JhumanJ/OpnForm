/**
 * ScaleInput tailwind-variants configuration
 */
export const scaleInputTheme = {
  slots: {
    button: [
      'cursor-pointer inline-block grow text-center',
      'text-neutral-700 dark:text-neutral-300',
      'border-neutral-300',
    ],
    buttonUnselected: [
      'bg-white border'
    ]
  },
  variants: {
    themeName: {
      default: {
        button: [
          'border-neutral-300 dark:border-neutral-600',
        ]
      },
      notion: {
        button: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light',
          'text-neutral-900 dark:text-neutral-100'
        ]
      }
    },
    size: {
      xs: { button: 'px-2 py-1 text-xs' },
      sm: { button: 'px-2 py-1.5 text-sm' },
      md: { button: 'px-3 py-2 text-base' },
      lg: { button: 'px-4 py-3 text-lg' }
    },
    borderRadius: {
      none: { button: 'rounded-none' },
      small: { button: 'rounded-lg' },
      full: { button: 'rounded-[20px]' }
    }
  },
  defaultVariants: {
    themeName: 'default',
    size: 'md',
    borderRadius: 'small'
  }
}
