export const cameraUploadTheme = {
  slots: {
    container: ['relative overflow-hidden'],
    media: ['w-full h-full object-cover'],
    panel: []
  },
  variants: {
    size: {
      xs: { media: 'min-h-20' },
      sm: { media: 'min-h-28' },
      md: { media: 'min-h-40' },
      lg: { media: 'min-h-58' }
    },
    borderRadius: {
      none: { container: 'rounded-none', media: 'rounded-none', panel: 'rounded-none' },
      small: { container: 'rounded-lg', media: 'rounded-lg', panel: 'rounded-lg' },
      full: { container: 'rounded-[20px]', media: 'rounded-[20px]', panel: 'rounded-[20px]' }
    }
  },
  defaultVariants: {
    size: 'md',
    borderRadius: 'small'
  }
}

