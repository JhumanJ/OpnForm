export const flatSelectInputTheme = {
  slots: {
    container: [
      'relative overflow-hidden',
      'border',
      'bg-white dark:bg-notion-dark-light',
      'text-neutral-700 dark:text-neutral-300',
      'focus:outline-hidden'
    ],
    option: [
      'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 flex items-center gap-x-2 border-t first:border-t-0'
    ],
    optionText: '',
    empty: [
      '!text-neutral-500 !cursor-not-allowed'
    ]
  },
  variants: {
    themeName: {
      default: {
        container: [
          'border-neutral-300 dark:border-neutral-600',
          'shadow-xs'
        ]
      },
      notion: {
        container: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background'
        ]
      }
    },
    size: {
      xs: { option: 'px-2.5 py-1.5', optionText: 'text-xs' },
      sm: { option: 'px-2 py-1.5', optionText: 'text-sm' },
      md: { option: 'px-4 py-2', optionText: 'text-base' },
      lg: { option: 'px-5 py-3', optionText: 'text-lg' }
    },
    borderRadius: {
      none: { container: 'rounded-none' },
      small: { container: 'rounded-lg' },
      full: { container: 'rounded-[20px]' }
    },
    hasError: { true: { container: '!ring-red-500 !ring-2 !border-transparent' } },
    disabled: { true: { container: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800' } }
  },
  defaultVariants: { themeName: 'default', size: 'md', borderRadius: 'small', hasError: false, disabled: false }
}

