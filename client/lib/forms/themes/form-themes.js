/**
 Input classes for each supported form themes
 */
export const themes = {
  default: {
    default: {
      wrapper: 'relative mb-3',
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100',
      inputSpacing: {
        vertical: 'py-2',
        horizontal: 'px-4'
      },
      help: 'text-gray-400 dark:text-gray-500',
      size: {
        lg: 'text-lg py-4 px-6',
        md: 'text-base py-2 px-4',
        sm: 'text-sm py-1.5 px-3'
      },
      borderRadius: {
        none: 'rounded-none',
        small: 'rounded-lg',
        full: 'rounded-[20px]'
      }
    },
    ScaleInput: {
      button: 'cursor-pointer text-gray-700 inline-block border-gray-300 flex-grow dark:bg-notion-dark-light dark:text-gray-300 text-center',
      unselectedButton: 'bg-white border'
    },
    SliderInput: {
      stepLabel: 'text-gray-700 dark:text-gray-300 text-center text-xs'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 filter hover:brightness-90'
    },
    CodeInput: {
      input: 'overflow-hidden'
    },
    RichTextAreaInput: {
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-1 focus:ring-opacity-100 focus:border-transparent focus:ring-2'
    },
    SelectInput: {
      input:
        'relative w-full flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 placeholder-gray-400 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-600 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent',
      dropdown: 'border border-gray-300 dark:border-gray-600',
      option: 'rounded'
    },
    DateInput: {
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100'
    },
    fileInput: {
      input:
        'min-h-40 border border-dashed border-gray-300 dark:border-gray-600 p-4 shadow-none',
      cameraInput: 'min-h-40',
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-gray-300 dark:border-gray-600 bg-white dark:bg-notion-dark-light rounded-lg shadow-sm max-w-[10rem]'
    }
  },
  simple: {
    default: {
      wrapper: 'relative mb-3',
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100',
      help: 'text-gray-400 dark:text-gray-500',
      inputSpacing: {
        vertical: 'py-2',
        horizontal: 'px-4'
      },
      size: {
        lg: 'text-lg py-4 px-6',
        md: 'text-base py-2 px-4',
        sm: 'text-sm py-1 px-2'
      },
      borderRadius: {
        none: 'rounded-none',
        small: 'rounded-lg',
        full: 'rounded-[20px]'
      }
    },
    ScaleInput: {
      button: 'flex-1 appearance-none border-gray-300 dark:border-gray-600 w-full bg-gray-50 text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 text-center',
      unselectedButton: 'bg-white border -mx-4'
    },
    SliderInput: {
      stepLabel: 'text-gray-700 dark:text-gray-300 text-center text-xs'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 filter hover:brightness-90'
    },
    CodeInput: {
      input: 'overflow-hidden'
    },
    RichTextAreaInput: {
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 text-base focus:outline-none focus:ring-1 focus:ring-opacity-100 focus:border-transparent focus:ring-2'
    },
    SelectInput: {
      input:
        'relative w-full flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 placeholder-gray-400 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-600 text-base focus:outline-none focus:ring-2 focus:border-transparent',
      dropdown: 'border border-gray-300 dark:border-gray-600',
      option: 'rounded'
    },
    DateInput: {
      input: 'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 placeholder-gray-400 text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100'
    },
    fileInput: {
      input:
        'min-h-40 border border-dashed border-gray-300 dark:border-gray-600 p-4 shadow-none',
      cameraInput: 'min-h-40',
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-gray-300 dark:border-gray-600 bg-white dark:bg-notion-dark-light shadow-sm max-w-[10rem]'
    }
  },
  notion: {
    default: {
      wrapper: 'relative mb-3',
      label: 'text-gray-900 dark:text-gray-100 mb-2 block mt-4',
      input:
        'rounded border-transparent flex-1 appearance-none shadow-inner-notion w-full bg-notion-input-background dark:bg-notion-dark-light text-gray-900 dark:text-gray-100 dark:placeholder-gray-500 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion',
      help: 'text-notion-input-help dark:text-gray-500',
      inputSpacing: {
        vertical: 'py-2',
        horizontal: 'px-4'
      },
      size: {
        md: 'text-base py-2 px-4',
        sm: 'text-sm py-1 px-2'
      },
      borderRadius: {
        none: 'rounded-none',
        small: 'rounded',
        full: 'rounded-[20px]'
      }
    },
    ScaleInput: {
      button: 'border-transparent flex-1 appearance-none shadow-inner-notion w-full bg-notion-input-background dark:bg-notion-dark-light text-gray-900 dark:text-gray-100 text-center',
      unselectedButton: 'bg-notion-input-background dark:bg-notion-dark-light border'
    },
    SliderInput: {
      stepLabel: 'text-gray-700 dark:text-gray-300 text-center text-xs'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold shadow shadow-inner-notion focus:outline-none focus:ring-2 focus:ring-offset-2 filter hover:brightness-90'
    },
    CodeInput: {
      input: 'shadow-inner-notion border-transparent focus:border-transparent overflow-hidden'
    },
    RichTextAreaInput: {
      input:
        'flex-1 appearance-none shadow-inner-notion  border-transparent focus:border-transparent w-full text-gray-900 bg-notion-input-background dark:bg-notion-dark-light dark:placeholder-gray-500 placeholder-gray-400 text-base focus:outline-none focus:ring-0 focus:ring-opacity-100 focus:border-transparent focus:ring-0 focus:shadow-focus-notion'
    },
    SelectInput: {
      input:
        'relative w-full border-transparent flex-1 appearance-none bg-notion-input-background shadow-inner-notion w-full text-gray-900 placeholder-gray-400 dark:bg-notion-dark-light dark:placeholder-gray-500 text-base focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion',
      dropdown: 'border border-gray-300 dark:border-gray-600',
      option: 'rounded'
    },
    DateInput: {
      input: 'shadow-inner-notion border-transparent focus:border-transparent flex-1 appearance-none w-full bg-notion-input-background dark:bg-notion-dark-light text-gray-900 dark:text-gray-100 placeholder-gray-400 text-base focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion p-[1px]'
    },
    fileInput: {
      input:
        'min-h-40 p-4 rounded bg-notion-input-background dark:bg-notion-dark',
      cameraInput: 'min-h-40 rounded',
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-gray-300 dark:border-gray-600 bg-white dark:bg-notion-dark-light rounded shadow-sm max-w-[10rem]'
    }
  }
}
