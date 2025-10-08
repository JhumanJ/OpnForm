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
    media: '',
    // classes for the media component root (e.g., <BlockMediaLayout />)
    mediaComponent: '',
    // classes for the underlying <img> inside BlockMediaLayout
    mediaImg: ''
  },
  variants: {
    borderRadius: {
      none: {},
      small: {},
      full: {}
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
    },
    mediaStyle: {
      default: {},
      intrinsic: {
        // apply intrinsic sizing to the component root and the image itself
        mediaComponent: 'inline-block align-top !w-auto !h-auto',
        mediaImg: 'inline-block h-auto w-auto max-w-[75%] max-h-120 object-contain transition-opacity duration-300'
      }
    }
  },
  compoundVariants: [
    // Default style: border radius on the container with overflow clipping
    { mediaStyle: 'default', borderRadius: 'none', class: { media: 'rounded-none overflow-hidden' } },
    { mediaStyle: 'default', borderRadius: 'small', class: { media: 'rounded-md overflow-hidden' } },
    { mediaStyle: 'default', borderRadius: 'full', class: { media: 'rounded-[20px] overflow-hidden' } },

    // Intrinsic style: border radius applied directly to the image
    { mediaStyle: 'intrinsic', borderRadius: 'none', class: { mediaImg: 'rounded-none' } },
    { mediaStyle: 'intrinsic', borderRadius: 'small', class: { mediaImg: 'rounded-md' } },
    { mediaStyle: 'intrinsic', borderRadius: 'full', class: { mediaImg: 'rounded-[20px]' } }
  ],
  defaultVariants: {
    size: 'md',
    borderRadius: 'small',
    mediaStyle: 'default'
  }
}