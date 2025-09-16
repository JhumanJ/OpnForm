/**
 * RatingInput tailwind-variants configuration
 */
export const ratingInputTheme = {
  slots: {
    icon: ''
  },
  variants: {
    size: {
      xs: { icon: 'w-4 h-4' },
      sm: { icon: 'w-6 h-6' },
      md: { icon: 'w-8 h-8' },
      lg: { icon: 'w-10 h-10' }
    }
  },
  defaultVariants: {
    size: 'md'
  }
}