export const dateInputTheme = {
  slots: {
    input: [
      'cursor-pointer overflow-hidden',
      'w-full flex items-center',
      'border',
      'bg-white dark:bg-notion-dark-light',
      'text-neutral-700 dark:text-neutral-300',
      'focus:outline-hidden'
    ]
  },
  variants: {
    themeName: {
      default: {
        input: [
          'border-neutral-300 dark:border-neutral-600',
          'shadow-xs'
        ]
      },
      notion: {
        input: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background'
        ]
      }
    },
    size: {
      xs: { input: 'px-2.5 py-1.5 text-xs' },
      sm: { input: 'px-2 py-1.5 text-sm' },
      md: { input: 'px-4 py-2 text-base' },
      lg: { input: 'px-5 py-3 text-lg' }
    },
    borderRadius: {
      none: { input: 'rounded-none' },
      small: { input: 'rounded-lg' },
      full: { input: 'rounded-[20px]' }
    },
    hasError: { true: { input: 'ring-red-500! ring-2! border-transparent!' } },
    disabled: { true: { input: '!cursor-not-allowed bg-neutral-200! dark:bg-neutral-800!' } },
    focused: { true: { input: 'ring-2 ring-form/100 border-transparent' } }
  },
  compoundVariants: [
    { themeName: 'notion', borderRadius: 'small', class: { input: '!rounded-xs' } }
  ],
  defaultVariants: { themeName: 'default', size: 'md', borderRadius: 'small', hasError: false, disabled: false, focused: false }
}

