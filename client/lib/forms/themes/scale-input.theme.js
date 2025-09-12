export const scaleInputTheme = {
  slots: {
    button: 'cursor-pointer text-neutral-700 inline-block border-neutral-300 grow dark:bg-notion-dark-light dark:text-neutral-300 text-center',
    unselectedButton: 'bg-white border',
    wrapper: 'rectangle-outer grid grid-cols-5 gap-2'
  },
  variants: {
    size: {
      xs: { button: 'text-xs px-2.5 py-1.5' },
      sm: { button: 'text-sm px-2 py-1.5' },
      md: { button: 'text-base px-4 py-2' },
      lg: { button: 'text-lg px-5 py-3' }
    },
    borderRadius: {
      none: { button: 'rounded-none' },
      small: { button: 'rounded-lg' },
      full: { button: 'rounded-[20px]' }
    }
  },
  defaultVariants: { size: 'md', borderRadius: 'small' }
}

