/**
 * SliderInput tailwind-variants configuration
 */
export const sliderInputTheme = {
  slots: {
    stepLabel: 'text-neutral-700 dark:text-neutral-300 text-center'
  },
  variants: {
    size: {
      xs: { stepLabel: 'text-xs' },
      sm: { stepLabel: 'text-sm' },
      md: { stepLabel: 'text-base' },
      lg: { stepLabel: 'text-lg' }
    }
  },
  // Legacy theme used text-xs; keep default aligned
  defaultVariants: {
    size: 'xs'
  }
}
