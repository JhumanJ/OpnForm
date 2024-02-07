const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin')

module.exports = {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
    "./error.vue",
    "./lib/forms/form-themes.js"
  ],
  safelist: [
    {
      pattern: /.*bg-(blue|gray|red|yellow|green).*/,
    },
    ...['green', 'red', 'blue', 'yellow'].map((color) => ['bg-' + color + '-100', 'border-' + color + '-500']).flat(), // Alerts
    ...['dark:hover:bg-notion-dark-light']
  ],
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    extend: {
      keyframes: {
        'bonce-slow': {
          '0%, 20%': {transform: 'translateY(0)'},
          '8%': {transform: 'translateY(-25%)'},
          '16%': {transform: 'translateY(+10%)'}
        },
        'infinite-scroll': {
          from: {transform: 'translateX(0)'},
          to: {transform: 'translateX(-100%)'},
        }
      },
      animation: {
        'bounce-slow': 'bonce-slow 3s ease-in-out infinite',
        'infinite-scroll': 'infinite-scroll 50s linear infinite',
      },
      maxHeight: {
        42: '10.5rem'
      },
      minHeight: {
        6: '1.5rem',
        8: '2rem'
      },
      maxWidth: {
        15: '15rem',
        10: '10rem',
        8: '2rem'
      },
      translate: {
        5.5: '1.4rem'
      },
      boxShadow: {
        'inner-notion': '#0f0f0f1a 0px 0px 0px 1px inset',
        'focus-notion': '#2eaadcb3 0px 0px 0px 1px inset, #2eaadc66 0px 0px 0px 2px !important'
      },
      colors: {
        gray: colors.slate,
        'nt-blue': {
          lighter: colors.blue['100'],
          light: colors.blue['300'],
          default: colors.blue['600'],
          DEFAULT: colors.blue['600'],
          dark: colors.blue['800']
        },
        'notion-dark': {
          DEFAULT: '#191919',
          light: '#2e2e2e'
        },
        'notion-input': {
          background: '#F7F6F3',
          backgroundDark: '#272B2C',
          help: '#37352f99',
          helpDark: '#fff9'
        },
        'form-color': 'var(--bg-form-color)'
      },
      transitionProperty: {
        height: 'height',
        width: 'width',
        maxWidth: 'max-width',
        spacing: 'margin, padding'
      }
    }
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
    plugin(function ({addVariant}) {
      addVariant('between', '&:not(:first-child):not(:last-child)')
      addVariant('hocus', ['&:hover', '&:focus'])
    })
  ]
}
