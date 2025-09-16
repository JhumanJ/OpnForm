/**
 * TextBlock tailwind-variants configuration
 */
export const textBlockTheme = {
  slots: {
    root: 'break-words whitespace-break-spaces'
  },
  variants: {
    size: {
      xs: { root: 'text-xs' },
      sm: { root: 'text-sm' },
      md: { root: 'text-base' },
      lg: { root: 'text-lg' }
    }
  },
  defaultVariants: {
    size: 'md'
  }
}

