/**
 * SignatureInput tailwind-variants configuration
 */
export const signatureInputTheme = {
  slots: {
    container: [
      'flex flex-wrap items-center justify-center gap-4',
      'focus:outline-hidden'
    ],
    help: 'text-neutral-500'
  },
  variants: {
    themeName: {
      default: {
        container: [
          'border border-neutral-300 dark:border-neutral-600',
          'bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300'
        ]
      },
      notion: {
        container: [
          'border border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light',
          'text-neutral-900 dark:text-neutral-100'
        ]
      }
    },
    size: {
      xs: { container: 'px-2.5 py-1.5 text-xs' },
      sm: { container: 'px-2 py-1.5 text-sm' },
      md: { container: 'px-4 py-2 text-base' },
      lg: { container: 'px-5 py-3 text-lg' }
    },
    borderRadius: {
      none: { container: 'rounded-none' },
      small: { container: 'rounded-lg' },
      full: { container: 'rounded-[20px]' }
    },
    hasError: {
      true: { container: '!ring-red-500 !ring-2 !border-transparent' }
    },
    disabled: {
      true: { container: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800' }
    }
  },
  defaultVariants: {
    themeName: 'default',
    size: 'md',
    borderRadius: 'small',
    hasError: false,
    disabled: false
  }
}

