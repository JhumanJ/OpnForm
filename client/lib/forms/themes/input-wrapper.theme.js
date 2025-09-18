/**
 * InputWrapper tailwind-variants configuration
 * Used for wrapping form inputs with labels, help text, and errors
 */
export const inputWrapperTheme = {
  slots: {
    wrapper: [
      'relative'
    ],
    help: 'text-neutral-500',
    media: 'rounded-md overflow-hidden'
  },
  variants: {
    borderRadius: {
      none: {
        media: 'rounded-none overflow-hidden'
      },
      small: {
        media: 'rounded-md overflow-hidden'
      },
      full: {
        media: 'rounded-[20px] overflow-hidden'
      }
    },
    size: {
      xs: {
        wrapper: 'my-0.5',
        media: 'mb-1'
      },
      sm: {
        wrapper: 'my-1',
        media: 'mb-1.5'
      },
      md: {
        wrapper: 'my-1.5',
        media: 'mb-2'
      },
      lg: {
        wrapper: 'my-1.5',
        media: 'mb-2.5'
      }
    }
  },
  defaultVariants: {
    size: 'md',
    borderRadius: 'small'
  }
}