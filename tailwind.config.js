const colors = require('tailwindcss/colors')

module.exports = {
  important: true,
  purge: {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue'
    ],
    options: {
      safelist: [
        /.*bg-(nt-blue|blue|gray|red|yellow|green).*/, // Buttons
        ...['green', 'red', 'blue', 'yellow'].map((color) => ['bg-' + color + '-100', 'border-' + color + '-500']).flat() // Alerts
      ]
    }
  },
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    extend: {
      keyframes: {
        'bonce-slow': {
          '0%, 20%': { transform: 'translateY(0)' },
          '8%': { transform: 'translateY(-25%)' },
          '16%': { transform: 'translateY(+10%)' }
        }
      },
      animation: {
        'bounce-slow': 'bonce-slow 3s ease-in-out infinite'
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
        gray: colors.blueGray,
        'nt-blue': {
          lighter: colors.blue['100'],
          light: colors.blue['300'],
          default: colors.blue['500'],
          DEFAULT: colors.blue['500'],
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
        }
      },
      transitionProperty: {
        height: 'height',
        width: 'width',
        spacing: 'margin, padding'
      }
    }
  },
  variants: {
    extend: {
      animation: ['hover'],
      brightness: ['hover', 'focus'],
      invert: ['dark'],
      maxWidth: ['hover'],
      display: ['group-hover', 'dark'],
      rotate: ['group-hover']
    }
  },
  plugins: []
}
