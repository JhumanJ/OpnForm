export const selectInputTheme = {
  slots: {
    text: ''
  },
  variants: {
    size: {
      xs: { text: 'text-xs' },
      sm: { text: 'text-sm' },
      md: { text: 'text-base' },
      lg: { text: 'text-lg' }
    }
  },
  defaultVariants: { size: 'md' }
}

