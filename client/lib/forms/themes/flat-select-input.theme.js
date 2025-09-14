/**
 * FlatSelectInput tailwind-variants configuration
 */
export const flatSelectInputTheme = {
  slots: {
    container: [
      'border bg-white dark:bg-notion-dark-light',
      'text-neutral-700 dark:text-neutral-300',
      'focus-within:outline-hidden'
    ],
    // Keep base minimal; themeName variants add interaction + spacing
    option: [
      'flex items-center',
      'border-t first:border-t-0 px-2'
    ],
    help: 'text-neutral-500'
  },
  variants: {
    themeName: {
      default: {
        container: [
          'border-neutral-300 dark:border-neutral-600',
          'shadow-xs'
        ],
        option: [
          'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900',
          'gap-x-2'
        ]
      },
      notion: {
        container: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light',
          'text-neutral-900 dark:text-neutral-100'
        ],
        option: [
          'cursor-pointer hover:backdrop-brightness-95',
          'space-x-2',
          'border-neutral-300'
        ]
      }
    },
    size: {
      xs: { option: 'px-2 py-1 text-xs' },
      sm: { option: 'px-2 py-1.5 text-sm' },
      md: { option: 'px-3 py-2 text-base' },
      lg: { option: 'px-4 py-3 text-lg' }
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
