/**
 * MatrixInput tailwind-variants configuration
 */
export const matrixInputTheme = {
  slots: {
    container: 'border overflow-x-auto',
    cell: '',
    cellHover: '',
    option: 'flex items-center justify-center relative',
    headerCell: 'w-full flex items-center justify-center',
    rowCell: 'w-full'
  },
  variants: {
    theme: {
      default: {
        container: [
          'border-neutral-300 dark:border-neutral-600',
          'bg-white dark:bg-notion-dark-light',
          'shadow-xs'
        ],
        cell: 'border-neutral-300 dark:border-neutral-600',
        cellHover: 'hover:bg-neutral-50 dark:hover:bg-neutral-900',
        option: 'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900'
      },
      simple: {
        container: [
          'border-neutral-300 dark:border-neutral-600',
          'bg-white dark:bg-notion-dark-light'
        ],
        cell: 'border-neutral-300 dark:border-neutral-600',
        cellHover: 'hover:bg-neutral-50 dark:hover:bg-neutral-900',
        option: 'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900'
      },
      notion: {
        container: [
          'border-notion-input-border dark:border-notion-input-borderDark',
          'bg-notion-input-background dark:bg-notion-dark-light'
        ],
        cell: 'border-notion-input-border dark:border-notion-input-borderDark',
        cellHover: 'hover:backdrop-brightness-95',
        option: 'cursor-pointer hover:backdrop-brightness-95'
      }
    },
    size: {
      xs: {
        headerCell: 'p-1 text-xs',
        rowCell: 'p-1 text-xs',
        option: 'p-1'
      },
      sm: {
        headerCell: 'p-1.5 text-sm',
        rowCell: 'p-1.5 text-sm',
        option: 'p-1.5'
      },
      md: {
        headerCell: 'p-2 text-base',
        rowCell: 'p-2 text-base',
        option: 'p-2'
      },
      lg: {
        headerCell: 'p-3 text-lg',
        rowCell: 'p-3 text-lg',
        option: 'p-3'
      }
    },
    borderRadius: {
      none: { container: 'rounded-none' },
      small: { container: 'rounded-lg' },
      full: { container: 'rounded-[20px]' }
    },
    hasError: {
      true: { 
        container: '!ring-red-500 !ring-2 !border-transparent',
        cell: '!border-red-500'
      }
    },
    disabled: {
      true: { 
        container: '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800',
        cellHover: '!hover:bg-neutral-200 dark:!hover:bg-neutral-800',
        option: '!cursor-not-allowed !hover:bg-neutral-200 dark:!hover:bg-neutral-800'
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

