/**
 * FileInput tailwind-variants configuration (also used by Barcode and Camera)
 */
export const fileInputTheme = {
  slots: {
    container: [
      'flex flex-col w-full items-center justify-center transition-colors duration-40',
      'border border-dashed',
      'shadow-none',
      'hover:bg-neutral-50 dark:hover:bg-notion-dark-light',
      'focus:outline-hidden focus:ring-2'
    ]
  },
  variants: {
    theme: {
      default: { container: 'border-neutral-300 dark:border-neutral-600' },
      minimal: { container: 'border-2 border-transparent bg-neutral-100 dark:bg-notion-dark-light text-neutral-700 dark:text-neutral-300 focus:ring-2 focus:ring-form/60' },
      notion: {
        container: 'border-notion-input-border dark:border-notion-input-borderDark bg-notion-input-background dark:bg-notion-dark-light text-neutral-900 dark:text-neutral-100'
      },
      transparent: {
        container: 'border-0 bg-transparent text-neutral-700 dark:text-neutral-300 rounded-none shadow-[inset_0_-1px_0_0_rgb(212_212_212)] dark:shadow-[inset_0_-1px_0_0_rgb(82_82_82)] transition-shadow duration-200 focus:ring-0 focus-within:ring-0 focus-within:shadow-[inset_0_-2px_0_0_var(--color-form)]'
      }
    },
    size: {
      xs: { container: 'min-h-20' },
      sm: { container: 'min-h-28' },
      md: { container: 'min-h-40' },
      lg: { container: 'min-h-48' }
    },
    borderRadius: {
      none: { container: 'rounded-none' },
      small: { container: 'rounded-lg' },
      full: { container: 'rounded-[20px]' }
    },
    hasError: {
      true: { container: 'border-red-500 border-2' }
    },
    disabled: {
      true: { container: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800' }
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

