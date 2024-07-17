/**
 Input classes for each supported form themes
 */
export const themes = {
  default: {
    default: {
      wrapper: {
        sm: 'relative mb-2',
        md: 'relative mb-3',
        lg: 'relative mb-3',
      },
      label: 'text-gray-700 dark:text-gray-300 font-medium',
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100',
      help: 'text-gray-500',
      spacing: {
        horizontal: {
          sm: 'px-2',
          md: 'px-4',
          lg: 'px-5'
        },
        vertical: {
          sm: 'py-1.5',
          md: 'py-2',
          lg: 'py-3'
        }
      },
      fontSize: {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
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
      option: 'rounded',
      minHeight: {
        sm: 'min-h-[20px]',
        md: 'min-h-[24px]',
        lg: 'min-h-[28px]'
      }
    },
    FlatSelectInput: {
      option: 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 flex items-center space-x-2 border-t first:border-t-0 px-2',
      unselectedIcon: 'text-gray-300 dark:text-gray-600',
      icon: {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6 mx-1'
      }
    },
    DateInput: {
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100'
    },
    CheckboxInput:{
      size: {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-5 h-5'
      },
    },
    SwitchInput:{
      containerSize: {
        sm: 'h-5 w-10 p-0.5',
        md: 'h-6 w-12 p-1',
        lg: 'h-6 w-12 p-1'
      },
      circleSize: {
        sm: 'h-4 w-4',
        md: 'h-4 w-4',
        lg: 'h-4 w-4'
      },
      translatedClass: {
        sm: 'translate-x-5',
        md: 'translate-x-6',
        lg: 'translate-x-6'
      }
    },
    RatingInput:{
      size: {
        sm: 'w-6 h-6',
        md: 'w-8 h-8',
        lg: 'w-10 h-10'
      },
    },
    fileInput: {
      input:
        'border border-dashed border-gray-300 dark:border-gray-600 p-4 shadow-none',
      minHeight: {
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-58'
      },
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-gray-300 dark:border-gray-600 bg-white dark:bg-notion-dark-light rounded-lg shadow-sm max-w-[10rem]'
    },
    SignatureInput: {
      minHeight: {
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
    }
  },
  simple: {
    default: {
      wrapper: {
        sm: 'relative mb-2',
        md: 'relative mb-3',
        lg: 'relative mb-3',
      },
      label: 'text-gray-700 dark:text-gray-300 font-medium',
      input:
        'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100',
      help: 'text-gray-500',
      spacing: {
        horizontal: {
          sm: 'px-2',
          md: 'px-4',
          lg: 'px-5'
        },
        vertical: {
          sm: 'py-1.5',
          md: 'py-2',
          lg: 'py-3'
        }
      },
      fontSize: {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
      },
      borderRadius: {
        none: 'rounded-none',
        small: 'rounded-lg',
        full: 'rounded-[20px]'
      }
    },
    ScaleInput: {
      button: 'flex-1 appearance-none border-gray-300 dark:border-gray-600 w-full bg-gray-50 text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 text-center',
      unselectedButton: 'bg-white border'
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
      option: 'rounded',
      minHeight: {
        sm: 'min-h-[20px]',
        md: 'min-h-[24px]',
        lg: 'min-h-[28px]'
      }
    },
    FlatSelectInput: {
      option: 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 flex items-center space-x-2 border-t first:border-t-0 px-2',
      unselectedIcon: 'text-gray-300 dark:text-gray-600',
      icon: {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6 mx-1'
      }
    },
    DateInput: {
      input: 'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 placeholder-gray-400 text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100'
    },
    CheckboxInput:{
      size: {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-5 h-5'
      },
    },
    SwitchInput:{
      containerSize: {
        sm: 'h-5 w-10',
        md: 'h-6 w-12',
        lg: 'h-6 w-12'
      },
      circleSize: {
        sm: 'h-4 w-4',
        md: 'h-4 w-4',
        lg: 'h-4 w-4'
      },
    },
    RatingInput:{
      size: {
        sm: 'w-6 h-6',
        md: 'w-8 h-8',
        lg: 'w-10 h-10'
      },
    },
    fileInput: {
      input:
        'border border-dashed border-gray-300 dark:border-gray-600 p-4 shadow-none',
      minHeight: {
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-gray-300 dark:border-gray-600 bg-white dark:bg-notion-dark-light shadow-sm max-w-[10rem]'
    },
    SignatureInput: {
      minHeight: {
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
    }
  },
  notion: {
    default: {
      wrapper: {
        sm: 'relative mb-2',
        md: 'relative mb-3',
        lg: 'relative mb-3',
      },
      label: 'text-gray-900 dark:text-gray-100 mb-1 block mt-4',
      input:
        'rounded border-transparent flex-1 appearance-none shadow-inner-notion w-full bg-notion-input-background dark:bg-notion-dark-light text-gray-900 dark:text-gray-100 dark:placeholder-gray-500 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion',
      help: 'text-gray-500',
      spacing: {
        horizontal: {
          sm: 'px-2',
          md: 'px-4',
          lg: 'px-5'
        },
        vertical: {
          sm: 'py-1.5',
          md: 'py-2',
          lg: 'py-3'
        }
      },
      fontSize: {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
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
      option: 'rounded',
      minHeight: {
        sm: 'min-h-[20px]',
        md: 'min-h-[24px]',
        lg: 'min-h-[28px]'
      }
    },
    FlatSelectInput: {
      option: 'cursor-pointer hover:backdrop-brightness-95 flex items-center space-x-2 border-t border-neutral-300 first:border-t-0 px-2',
      unselectedIcon: 'text-neutral-300 dark:text-neutral-600',
      icon: {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6 mx-1'
      }
    },
    DateInput: {
      input: 'shadow-inner-notion border-transparent focus:border-transparent flex-1 appearance-none w-full bg-notion-input-background dark:bg-notion-dark-light text-gray-900 dark:text-gray-100 placeholder-gray-400 text-base focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion p-[1px]'
    },
    CheckboxInput:{
      size: {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-5 h-5'
      },
    },
    SwitchInput:{
      containerSize: {
        sm: 'h-5 w-10',
        md: 'h-6 w-12',
        lg: 'h-6 w-12'
      },
      circleSize: {
        sm: 'h-4 w-4',
        md: 'h-4 w-4',
        lg: 'h-4 w-4'
      },
    },
    RatingInput:{
      size: {
        sm: 'w-6 h-6',
        md: 'w-8 h-8',
        lg: 'w-10 h-10'
      },
    },
    fileInput: {
      input:
        'p-4 rounded bg-notion-input-background dark:bg-notion-dark',
      minHeight: {
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-gray-300 dark:border-gray-600 bg-white dark:bg-notion-dark-light rounded shadow-sm max-w-[10rem]'
    },
    SignatureInput: {
      minHeight: {
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
    }
  }
}
