/**
 * MatrixInput tailwind-variants configuration
 */
export const matrixInputTheme = {
  slots: {
    container: 'border overflow-x-auto',
    cell: '',
    cellHover: '',
    option: 'flex items-center justify-center relative'
  },
  variants: {
    theme: {
      default: {
        container: 'bg-white dark:bg-notion-dark-light shadow-xs',
        cell: 'border-neutral-300 dark:border-neutral-600',
        cellHover: 'hover:bg-neutral-50 dark:hover:bg-neutral-900',
        option: 'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900'
      },
      notion: {
        container: 'bg-notion-input-background dark:bg-notion-dark-light',
        cell: 'border-notion-input-border dark:border-notion-input-borderDark',
        cellHover: 'hover:backdrop-brightness-95',
        option: 'cursor-pointer hover:backdrop-brightness-95'
      }
    },
    borderRadius: {
      none: { container: 'rounded-none' },
      small: { container: 'rounded-lg' },
      full: { container: 'rounded-[20px]' }
    }
  },
  defaultVariants: {
    theme: 'default',
    borderRadius: 'small'
  }
}

