/**
 * PaymentInput tailwind-variants configuration
 */
export const paymentInputTheme = {
  slots: {
    container: [],
    amountBar: ['mb-4 flex items-center justify-between']
  },
  variants: {
    themeName: {
      default: {
        container: [
          'border border-neutral-300 dark:border-neutral-600',
          'bg-white dark:bg-neutral-900',
          'text-neutral-700 dark:text-neutral-200'
        ],
        amountBar: [
          'border border-neutral-300 dark:border-neutral-600',
          'bg-neutral-50 dark:bg-neutral-800'
        ]
      },
      notion: {
        container: [
          'border border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light',
          'text-neutral-900 dark:text-neutral-100'
        ],
        amountBar: [
          'border border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light'
        ]
      }
    },
    size: {
      xs: { container: 'px-2.5 py-1.5 text-xs', amountBar: 'px-2.5 py-1.5 text-xs' },
      sm: { container: 'px-2 py-1.5 text-sm', amountBar: 'px-2 py-1.5 text-sm' },
      md: { container: 'px-4 py-2 text-base', amountBar: 'px-4 py-2 text-base' },
      lg: { container: 'px-5 py-3 text-lg', amountBar: 'px-5 py-3 text-lg' }
    },
    borderRadius: {
      none: { container: 'rounded-none', amountBar: 'rounded-none' },
      small: { container: 'rounded-lg', amountBar: 'rounded-lg' },
      full: { container: 'rounded-[20px]', amountBar: 'rounded-[20px]' }
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

