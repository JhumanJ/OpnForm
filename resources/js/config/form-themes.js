/**
  Input classes for each supported form themes
 */
export const themes = {
  default: {
    default: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'rounded-lg border-gray-300 flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100',
      help: 'text-gray-400 dark:text-gray-500'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg filter hover:brightness-110'
    },
    CodeInput: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden',
      help: 'text-gray-400 dark:text-gray-500'
    },
    RichTextAreaInput: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'rounded-lg border-gray-300 flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-1 focus:ring-opacity-100 focus:border-transparent focus:ring-2',
      help: 'text-gray-400 dark:text-gray-500'
    },
    SelectInput: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'relative w-full rounded-lg border-gray-300 flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full px-4 bg-white text-gray-700 placeholder-gray-400 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-600 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent',
      help: 'text-gray-400 dark:text-gray-500'
    }
  },
  simple: {
    default: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-2 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100',
      help: 'text-gray-400 dark:text-gray-500'
    },
    Button: {
      body: 'transition ease-in duration-200 text-center font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 filter hover:brightness-110'
    },
    SelectInput: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'relative w-full flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full px-2 bg-white text-gray-700 placeholder-gray-400 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-600 text-base focus:outline-none focus:ring-2 focus:border-transparent',
      help: 'text-gray-400 dark:text-gray-500'
    },
    CodeInput: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'border border-gray-300 dark:border-gray-600 overflow-hidden',
      help: 'text-gray-400 dark:text-gray-500'
    },
    RichTextAreaInput: {
      label: 'text-gray-700 dark:text-gray-300 font-semibold',
      input: 'border-transparent flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 text-base focus:outline-none focus:ring-1 focus:ring-opacity-100 focus:border-transparent focus:ring-2',
      help: 'text-gray-400 dark:text-gray-500'
    }
  },
  notion: {
    default: {
      label: 'text-gray-900 dark:text-gray-100 mb-2 block mt-4',
      input: 'rounded border-transparent flex-1 appearance-none shadow-inner-notion w-full py-2 px-2 bg-notion-input-background dark:bg-notion-dark-light text-gray-900 dark:text-gray-100 dark:placeholder-gray-500 placeholder-gray-400 text-base focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion',
      help: 'text-notion-input-help dark:text-gray-500'
    },
    Button: {
      body: 'rounded-md transition ease-in duration-200 text-center font-semibold shadow shadow-inner-notion focus:outline-none focus:ring-2 focus:ring-offset-2 filter hover:brightness-110'
    },
    SelectInput: {
      label: 'text-gray-900 dark:text-gray-100 mb-2 block mt-4',
      input: 'rounded relative w-full border-transparent flex-1 appearance-none bg-notion-input-background shadow-inner-notion w-full px-2 text-gray-900 placeholder-gray-400 dark:bg-notion-dark-light dark:placeholder-gray-500 text-base focus:outline-none focus:ring-0 focus:border-transparent focus:shadow-focus-notion',
      help: 'text-notion-input-help dark:text-gray-500'
    },
    CodeInput: {
      label: 'text-gray-900 dark:text-gray-100 mb-2 block mt-4',
      input: 'rounded shadow-inner-notion border border-gray-300 dark:border-gray-600 overflow-hidden',
      help: 'text-notion-input-help dark:text-gray-500'
    },
    RichTextAreaInput: {
      label: 'text-gray-900 dark:text-gray-100 mb-2 block mt-4',
      input: 'rounded border-transparent flex-1 appearance-none shadow-inner-notion border border-gray-300 dark:border-gray-600 w-full text-gray-900 bg-notion-input-background dark:bg-notion-dark-light shadow-inner dark:placeholder-gray-500 placeholder-gray-400 text-base focus:outline-none focus:ring-0 focus:ring-opacity-100 focus:border-transparent focus:ring-0 focus:shadow-focus-notion',
      help: 'text-notion-input-help dark:text-gray-500'
    }
  }

}
