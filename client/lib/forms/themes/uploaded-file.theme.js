/**
 * UploadedFile tailwind-variants configuration
 */
export const uploadedFileTheme = {
  slots: {
    root: 'border rounded-lg shadow-xs max-w-10'
  },
  variants: {
    themeName: {
      default: { root: 'border-neutral-300 dark:border-neutral-600 dark:bg-notion-dark-light' },
      notion: { root: 'border-notion-input-border dark:border-notion-input-borderDark dark:bg-notion-dark-light rounded-xs' }
    }
  },
  defaultVariants: {
    themeName: 'default'
  }
}

