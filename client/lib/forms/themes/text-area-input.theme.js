export const textAreaInputTheme = {
  slots: {
    textarea: [
      'resize-y block',
      'flex-1 appearance-none w-full',
      'border',
      'bg-white dark:bg-notion-dark-light',
      'text-neutral-700 dark:text-neutral-300',
      'placeholder-neutral-400 dark:placeholder-neutral-500',
      'focus:outline-hidden',
      'disabled:cursor-not-allowed disabled:opacity-75'
    ],
    help: 'text-neutral-500'
  },
  variants: {
    themeName: {
      default: {
        textarea: [
          'border-neutral-300 dark:border-neutral-600',
          'shadow-xs',
          'focus:ring-2 focus:ring-form/100 focus:border-transparent'
        ]
      },
      simple: {
        textarea: [
          'border-neutral-300 dark:border-neutral-600',
          'focus:ring-2 focus:ring-form/100 focus:border-transparent'
        ]
      },
      notion: {
        textarea: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light',
          'text-neutral-900 dark:text-neutral-100',
          'focus:ring-2 focus:ring-form/40 focus:border-transparent'
        ]
      }
    },
    size: {
      xs: { textarea: 'px-2.5 py-1.5 text-xs' },
      sm: { textarea: 'px-2 py-1.5 text-sm' },
      md: { textarea: 'px-4 py-2 text-base' },
      lg: { textarea: 'px-5 py-3 text-lg' }
    },
    borderRadius: {
      none: { textarea: 'rounded-none' },
      small: { textarea: 'rounded-lg' },
      full: { textarea: 'rounded-[20px]' }
    },
    hasError: { true: { textarea: '!ring-red-500 !ring-2 !border-transparent' } },
    disabled: { true: { textarea: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800' } }
  },
  compoundVariants: [
    { themeName: 'notion', borderRadius: 'small', class: { textarea: '!rounded-xs' } }
  ],
  defaultVariants: { themeName: 'default', size: 'md', borderRadius: 'small', hasError: false, disabled: false }
}

