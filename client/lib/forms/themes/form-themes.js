/**
 Input classes for each supported form themes
 */
 export const themes = {
  default: {
    default: {
      wrapper: {
        xs: 'relative my-0.5',
        sm: 'relative my-1',
        md: 'relative my-1.5',
        lg: 'relative my-1.5',
      },
      label: 'text-neutral-700 dark:text-neutral-300 font-semibold',
      input:
        'flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 dark:placeholder-neutral-500 placeholder-neutral-400 shadow-xs focus:outline-hidden focus:ring-2 focus:ring-form/100 focus:border-transparent',
      help: 'text-neutral-500',
      spacing: {
        horizontal: {
          xs: 'px-2.5',
          sm: 'px-2',
          md: 'px-4',
          lg: 'px-5'
        },
        vertical: {
          xs: 'py-1.5',
          sm: 'py-1.5',
          md: 'py-2',
          lg: 'py-3'
        }
      },
      fontSize: {
        xs: 'text-xs',
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
      button: 'cursor-pointer text-neutral-700 inline-block border-neutral-300 grow dark:bg-notion-dark-light dark:text-neutral-300 text-center',
      unselectedButton: 'bg-white border'
    },
    SliderInput: {
      stepLabel: 'text-neutral-700 dark:text-neutral-300 text-center text-xs'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold shadow-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 hover:brightness-90'
    },
    CodeInput: {
      input: 'overflow-hidden'
    },
    RichTextAreaInput: {
      input:
        'flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 dark:placeholder-neutral-500 placeholder-neutral-400 shadow-xs text-base focus:outline-hidden focus:ring-1 focus:ring-form/100 focus:border-transparent focus:ring-2'
    },
    SelectInput: {
      input:
        'relative w-full flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full text-neutral-700 dark:text-neutral-300 dark:placeholder-neutral-600 shadow-xs text-base focus:outline-hidden focus:ring-2 focus:border-transparent',
      background: 'bg-white dark:bg-notion-dark-light',
      chevronGradient: 'bg-gradient-to-r from-transparent to-white dark:to-notion-dark-light',
      dropdown: 'border border-neutral-300 dark:border-neutral-600',
      option: 'rounded-xs',
      minHeight: {
        xs: 'min-h-[16px]',
        sm: 'min-h-[20px]',
        md: 'min-h-[24px]',
        lg: 'min-h-[28px]'
      }
    },
     PhoneInput: {
        countrySelectWidth: {
          xs: 'w-[80px]',
          sm: 'w-[100px]',
          md: 'w-[120px]',
          lg: 'w-[120px]'
        },
        flag: {
          xs: '-mt-[16px]!',
          sm: '-mt-[14px]!',
          md: '-mt-[9px]! rounded-xs',
          lg: '-mt-[9px]! rounded-xs'
        },
        flagSize: {
          xs: 'small',
          sm: 'small',
          md: 'normal',
          lg: 'normal'
        },
        maxHeight: {
          xs: 'max-h-[16px]',
          sm: 'max-h-[20px]',
          md: 'max-h-[24px]',
          lg: 'max-h-[28px]'
        }
     },
    FlatSelectInput: {
      option: 'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 flex items-center gap-x-2 border-t first:border-t-0 px-2',
      unselectedIcon: 'text-neutral-300 dark:text-neutral-600',
      icon: {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6 mx-1'
      }
    },
    DateInput: {
      input:
        'flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 dark:placeholder-neutral-500 placeholder-neutral-400 shadow-xs text-base focus:outline-hidden focus:ring-2 focus:ring-form/100 focus:border-transparent'
    },
    PaymentInput: {
      cardContainer: 'transition-all duration-200',
      focusRing: 'ring-2 ring-form/100 border-transparent'
    },
    CheckboxInput:{
      size: {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-5 h-5'
      },
    },
    SwitchInput:{
      containerSize: {
        xs: 'h-4 w-8 p-0.5',
        sm: 'h-5 w-10 p-0.5',
        md: 'h-6 w-12 p-1',
        lg: 'h-6 w-12 p-1'
      },
      circleSize: {
        xs: 'h-3 w-3',
        sm: 'h-4 w-4',
        md: 'h-4 w-4',
        lg: 'h-4 w-4'
      },
      translatedClass: {
        xs: 'translate-x-4',
        sm: 'translate-x-5',
        md: 'translate-x-6',
        lg: 'translate-x-6'
      }
    },
    RatingInput:{
      size: {
        xs: 'w-4 h-4',
        sm: 'w-6 h-6',
        md: 'w-8 h-8',
        lg: 'w-10 h-10'
      },
    },
    fileInput: {
      input:
        'border border-dashed border-neutral-300 dark:border-neutral-600 p-4 shadow-none',
      minHeight: {
        xs: 'min-h-20',
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-58'
      },
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-notion-dark-light rounded-lg shadow-xs max-w-10'
    },
    SignatureInput: {
      minHeight: {
        xs: 'min-h-20',
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
    },
    MatrixInput: {
      table: 'bg-white dark:bg-notion-dark-light shadow-xs',
      cell: 'border-neutral-300 dark:border-neutral-600',
      cellHover: 'hover:bg-neutral-50 dark:hover:bg-neutral-900'
    }
  },
  simple: {
    default: {
      wrapper: {
        xs: 'relative my-0.5',
        sm: 'relative my-1',
        md: 'relative my-1.5',
        lg: 'relative my-1.5',
      },
      label: 'text-neutral-700 dark:text-neutral-300 font-medium',
      input:
        'flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 dark:placeholder-neutral-500 placeholder-neutral-400 focus:outline-hidden focus:ring-2 focus:ring-form/100 focus:border-transparent',
      help: 'text-neutral-500',
      spacing: {
        horizontal: {
          xs: 'px-2.5',
          sm: 'px-2',
          md: 'px-4',
          lg: 'px-5'
        },
        vertical: {
          xs: 'py-1.5',
          sm: 'py-1.5',
          md: 'py-2',
          lg: 'py-3'
        }
      },
      fontSize: {
        xs: 'text-xs',
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
      button: 'flex-1 appearance-none border-neutral-300 dark:border-neutral-600 w-full bg-neutral-50 text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 text-center',
      unselectedButton: 'bg-white border'
    },
    SliderInput: {
      stepLabel: 'text-neutral-700 dark:text-neutral-300 text-center text-xs'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold focus:outline-hidden focus:ring-2 focus:ring-offset-2 hover:brightness-90'
    },
    CodeInput: {
      input: 'overflow-hidden'
    },
    RichTextAreaInput: {
      input:
        'flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 dark:placeholder-neutral-500 placeholder-neutral-400 text-base focus:outline-hidden focus:ring-1 focus:ring-form/100 focus:border-transparent focus:ring-2'
    },
    SelectInput: {
      input:
        'relative w-full flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full text-neutral-700 dark:text-neutral-300 dark:placeholder-neutral-600 text-base focus:outline-hidden focus:ring-2 focus:border-transparent',
      background: 'bg-white dark:bg-notion-dark-light',
      chevronGradient: 'bg-gradient-to-r from-transparent to-white dark:to-notion-dark-light',
      dropdown: 'border border-neutral-300 dark:border-neutral-600',
      option: 'rounded-xs',
      minHeight: {
        xs: 'min-h-[16px]',
        sm: 'min-h-[20px]',
        md: 'min-h-[24px]',
        lg: 'min-h-[28px]'
      }
    },
    PhoneInput: {
      countrySelectWidth: {
        xs: 'w-[80px]',
          sm: 'w-[100px]',
        md: 'w-[120px]',
        lg: 'w-[120px]'
      },
      flag: {
        xs: '-mt-[16px]!',
          sm: '-mt-[14px]!',
        md: '-mt-[9px]! rounded-xs',
        lg: '-mt-[9px]! rounded-xs'
      },
      flagSize: {
        xs: 'small',
          sm: 'small',
        md: 'normal',
        lg: 'normal'
      },
      maxHeight: {
        xs: 'max-h-[16px]',
          sm: 'max-h-[20px]',
        md: 'max-h-[24px]',
        lg: 'max-h-[28px]'
      }
    },
    FlatSelectInput: {
      option: 'cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 flex items-center space-x-2 border-t first:border-t-0 px-2',
      unselectedIcon: 'text-neutral-300 dark:text-neutral-600',
      icon: {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6 mx-1'
      }
    },
    DateInput: {
      input: 'flex-1 appearance-none border border-neutral-300 dark:border-neutral-600 w-full bg-white text-neutral-700 dark:bg-notion-dark-light dark:text-neutral-300 placeholder-neutral-400 text-base focus:outline-hidden focus:ring-2 focus:ring-form/100 focus:border-transparent'
    },
    PaymentInput: {
      cardContainer: 'transition-all duration-200',
      focusRing: 'ring-2 ring-form/100 border-transparent'
    },
    CheckboxInput:{
      size: {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-5 h-5'
      },
    },
    SwitchInput:{
      containerSize: {
        xs: 'h-4 w-8',
        sm: 'h-5 w-10',
        md: 'h-6 w-12',
        lg: 'h-6 w-12'
      },
      circleSize: {
        xs: 'h-3 w-3',
        sm: 'h-4 w-4',
        md: 'h-4 w-4',
        lg: 'h-4 w-4'
      },
    },
    RatingInput:{
      size: {
        xs: 'w-4 h-4',
        sm: 'w-6 h-6',
        md: 'w-8 h-8',
        lg: 'w-10 h-10'
      },
    },
    fileInput: {
      input:
        'border border-dashed border-neutral-300 dark:border-neutral-600 p-4 shadow-none',
      minHeight: {
        xs: 'min-h-20',
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-notion-dark-light shadow-xs max-w-10'
    },
    SignatureInput: {
      minHeight: {
        xs: 'min-h-20',
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
    },
    MatrixInput: {
      table: 'bg-white dark:bg-notion-dark-light',
      cell: 'border-neutral-300 dark:border-neutral-600',
      cellHover: 'hover:bg-neutral-50 dark:hover:bg-neutral-900'
    }
  },
  notion: {
    default: {
      wrapper: {
        xs: 'relative my-0.5',
        sm: 'relative my-1',
        md: 'relative my-1.5',
        lg: 'relative my-1.5',
      },
      label: 'text-neutral-900 dark:text-neutral-100 mb-1 block mt-4',
      input:
        'rounded-xs border border-notion-input-border dark:border-notion-input-borderDark flex-1 appearance-none w-full bg-notion-input-background dark:bg-notion-dark-light text-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500 placeholder-neutral-400 focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent',
      help: 'text-neutral-500',
      spacing: {
        horizontal: {
          xs: 'px-2.5',
          sm: 'px-2',
          md: 'px-4',
          lg: 'px-5'
        },
        vertical: {
          xs: 'py-1.5',
          sm: 'py-1.5',
          md: 'py-2',
          lg: 'py-3'
        }
      },
      fontSize: {
        xs: 'text-xs',
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
      },
      borderRadius: {
        none: 'rounded-none',
        small: 'rounded-xs',
        full: 'rounded-[20px]'
      }
    },
    ScaleInput: {
      button: 'border border-notion-input-border dark:border-notion-input-borderDark flex-1 appearance-none w-full bg-notion-input-background dark:bg-notion-dark-light text-neutral-900 dark:text-neutral-100 text-center focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent',
      unselectedButton: 'bg-notion-input-background dark:bg-notion-dark-light'
    },
    SliderInput: {
      stepLabel: 'text-neutral-700 dark:text-neutral-300 text-center text-xs'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold border border-notion-input-border dark:border-notion-input-borderDark focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent hover:brightness-90'
    },
    CodeInput: {
      input: 'border border-notion-input-border dark:border-notion-input-borderDark focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent overflow-hidden'
    },
    RichTextAreaInput: {
      input:
        'flex-1 appearance-none border border-notion-input-border dark:border-notion-input-borderDark w-full bg-notion-input-background dark:bg-notion-dark-light text-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500 placeholder-neutral-400 text-base focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent'
    },
    SelectInput: {
      input:
        'relative w-full border border-notion-input-border dark:border-notion-input-borderDark flex-1 appearance-none w-full text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 text-base focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent',
      background: 'bg-notion-input-background dark:bg-notion-dark-light',
      chevronGradient: 'bg-gradient-to-r from-transparent to-notion-input-background dark:to-notion-dark-light',
      dropdown: 'border border-notion-input-border dark:border-notion-input-borderDark',
      option: 'rounded-xs',
      minHeight: {
        xs: 'min-h-[16px]',
        sm: 'min-h-[20px]',
        md: 'min-h-[24px]',
        lg: 'min-h-[28px]'
      }
    },
    PhoneInput: {
      countrySelectWidth: {
        xs: 'w-[80px]',
          sm: 'w-[100px]',
        md: 'w-[120px]',
        lg: 'w-[120px]'
      },
      flag: {
        xs: '-mt-[16px]!',
          sm: '-mt-[14px]!',
        md: '-mt-[9px]! rounded-xs',
        lg: '-mt-[9px]! rounded-xs'
      },
      flagSize: {
        xs: 'small',
          sm: 'small',
        md: 'normal',
        lg: 'normal'
      },
      maxHeight: {
        xs: 'max-h-[16px]',
          sm: 'max-h-[20px]',
        md: 'max-h-[24px]',
        lg: 'max-h-[28px]'
      }
    },
    FlatSelectInput: {
      option: 'cursor-pointer hover:backdrop-brightness-95 flex items-center space-x-2 border-t border-neutral-300 first:border-t-0 px-2',
      unselectedIcon: 'text-neutral-300 dark:text-neutral-600',
      icon: {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6 mx-1'
      }
    },
    DateInput: {
      input: 'border border-notion-input-border dark:border-notion-input-borderDark flex-1 appearance-none w-full bg-notion-input-background dark:bg-notion-dark-light text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 text-base focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent'
    },
    PaymentInput: {
      cardContainer: 'transition-all duration-200',
      focusRing: 'ring-2 ring-form/40 border-transparent'
    },
    CheckboxInput:{
      size: {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-5 h-5'
      },
    },
    SwitchInput:{
      containerSize: {
        xs: 'h-4 w-8',
        sm: 'h-5 w-10',
        md: 'h-6 w-12',
        lg: 'h-6 w-12'
      },
      circleSize: {
        xs: 'h-3 w-3',
        sm: 'h-4 w-4',
        md: 'h-4 w-4',
        lg: 'h-4 w-4'
      },
      translatedClass: {
        xs: 'translate-x-4',
        sm: 'translate-x-5',
        md: 'translate-x-6',
        lg: 'translate-x-6'
      }
    },
    RatingInput:{
      size: {
        xs: 'w-4 h-4',
        sm: 'w-6 h-6',
        md: 'w-8 h-8',
        lg: 'w-10 h-10'
      },
    },
    fileInput: {
      input:
        'p-4 rounded-xs border border-dashed border-notion-input-border dark:border-notion-input-borderDark bg-notion-input-background dark:bg-notion-dark-light focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent',
      minHeight: {
        xs: 'min-h-20',
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
      inputHover: {
        light: 'bg-neutral-50',
        dark: 'bg-notion-dark-light'
      },
      uploadedFile:
        'border border-notion-input-border dark:border-notion-input-borderDark bg-white dark:bg-notion-dark-light rounded-xs max-w-10 focus:outline-hidden focus:ring-2 focus:ring-form/40 focus:border-transparent'
    },
    SignatureInput: {
      minHeight: {
        xs: 'min-h-20',
        sm: 'min-h-28',
        md: 'min-h-40',
        lg: 'min-h-48'
      },
    },
    MatrixInput: {
      table: 'bg-notion-input-background dark:bg-notion-dark-light',
      cell: 'border-notion-input-border dark:border-notion-input-borderDark',
      cellHover: 'hover:backdrop-brightness-95'
    }
  }
}
