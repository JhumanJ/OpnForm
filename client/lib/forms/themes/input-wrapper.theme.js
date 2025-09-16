/**
 * InputWrapper tailwind-variants configuration
 * Used for wrapping form inputs with labels, help text, and errors
 */
export const inputWrapperTheme = {
  slots: {
    wrapper: [
      'relative'
    ],
    help: 'text-neutral-500'
  },
  variants: {
    size: {
      xs: {
        wrapper: 'my-0.5'
      },
      sm: {
        wrapper: 'my-1'
      },
      md: {
        wrapper: 'my-1.5'
      },
      lg: {
        wrapper: 'my-1.5'
      }
    }
  },
  defaultVariants: {
    size: 'md'
  }
}