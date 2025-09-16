/**
 * PhoneInput tailwind-variants configuration
 */
export const phoneInputTheme = {
  slots: {
    countrySelectWidth: '',
    selectedMaxHeight: '',
    flag: '',
    input: [
      'grow',
      'border',
      'bg-white dark:bg-notion-dark-light',
      'text-neutral-700 dark:text-neutral-300',
      'focus:outline-hidden',
      'placeholder-neutral-400 dark:placeholder-neutral-500'
    ]
  },
  variants: {
    theme: {
      default: { 
        input: [
          'border-neutral-300 dark:border-neutral-600',
          'shadow-xs',
          'focus:ring-2 focus:ring-form/100 focus:border-transparent'
        ]
      },
      simple: {
        input: [
          'border-neutral-300 dark:border-neutral-600',
          'focus:ring-2 focus:ring-form/100 focus:border-transparent'
        ]
      },
      notion: {
        input: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light',
          'text-neutral-900 dark:text-neutral-100',
          'focus:ring-2 focus:ring-form/40 focus:border-transparent'
        ]
      }
    },
    size: {
      xs: { 
        input: 'px-2.5 py-1.5 text-xs',
        countrySelectWidth: 'w-[80px]',
        selectedMaxHeight: 'max-h-[16px]',
        flag: '-mt-[16px]!'
      },
      sm: { 
        input: 'px-2 py-1.5 text-sm',
        countrySelectWidth: 'w-[100px]',
        selectedMaxHeight: 'max-h-[20px]',
        flag: '-mt-[14px]!'
      },
      md: { 
        input: 'px-4 py-2 text-base',
        countrySelectWidth: 'w-[120px]',
        selectedMaxHeight: 'max-h-[24px]',
        flag: '-mt-[9px]! rounded-xs'
      },
      lg: { 
        input: 'px-5 py-3 text-lg',
        countrySelectWidth: 'w-[120px]',
        selectedMaxHeight: 'max-h-[28px]',
        flag: '-mt-[9px]! rounded-xs'
      }
    },
    borderRadius: {
      none: { input: 'rounded-none' },
      small: { input: 'rounded-lg' },
      full: { input: 'rounded-[20px]' }
    },
    hasError: {
      true: { input: '!ring-red-500 !ring-2 !border-transparent' }
    },
    disabled: {
      true: { input: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800' }
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
