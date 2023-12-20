<template>
  <NuxtLink v-if="href" :class="btnClasses" :href="href" :target="target">
    <slot />
  </NuxtLink>
  <button v-else-if="!to" :type="nativeType" :disabled="loading?true:null" :class="btnClasses">
    <template v-if="!loading">
      <span class="no-underline mx-auto">
        <slot />
      </span>
      <svg v-if="arrow" class="ml-2 w-3 h-3 inline" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 11L11 1M11 1H1M11 1V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round"
        />
      </svg>
    </template>
    <Loader v-else class="h-6 w-6 mx-auto" :class="`text-${colorShades['text']}`" />
  </button>
  <NuxtLink v-else :class="btnClasses" :to="to" :target="target">
    <span class="no-underline mx-auto">
      <slot />
    </span>
    <svg v-if="arrow" class="ml-2 w-3 h-3 inline" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M1 11L11 1M11 1H1M11 1V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round"
      />
    </svg>
  </NuxtLink>
</template>

<script>
export default {
  name: 'VButton',

  props: {
    color: {
      type: String,
      default: 'blue'
    },

    size: {
      type: String,
      default: 'medium'
    },

    nativeType: {
      type: String,
      default: null
    },

    loading: {
      type: Boolean,
      default: false
    },

    arrow: {
      type: Boolean,
      default: false
    },

    to: {
      type: Object,
      default: null
    },

    href: {
      type: String,
      default: null
    },

    target: {
      type: String,
      default: '_self'
    }
  },

  computed: {
    btnClasses () {
      const sizes = this.sizes
      const colorShades = this.colorShades
      return `v-btn ${sizes['p-y']} ${sizes['p-x']}
        ${colorShades?.main} ${colorShades?.hover} ${colorShades?.ring} ${colorShades['ring-offset']}
        ${colorShades?.text} transition ease-in duration-200 text-center text-${sizes?.font} font-medium focus:outline-none focus:ring-2
        focus:ring-offset-2 rounded-lg flex items-center hover:no-underline`
    },
    colorShades () {
      if (this.color === 'blue') {
        return {
          main: 'bg-blue-600',
          hover: 'hover:bg-blue-700',
          ring: 'focus:ring-blue-500',
          'ring-offset': 'focus:ring-offset-blue-200',
          text: 'text-white'
        }
      } else if (this.color === 'outline-blue') {
        return {
          main: 'bg-transparent border border-blue-600',
          hover: 'hover:bg-blue-600',
          ring: 'focus:ring-blue-500',
          'ring-offset': 'focus:ring-offset-blue-200',
          text: 'text-blue-600 hover:text-white'
        }
      } else if (this.color === 'outline-gray') {
        return {
          main: 'bg-transparent border border-gray-300',
          hover: 'hover:bg-gray-500',
          ring: 'focus:ring-gray-500',
          'ring-offset': 'focus:ring-offset-gray-200',
          text: 'text-gray-500 hover:text-white'
        }
      } else if (this.color === 'red') {
        return {
          main: 'bg-red-600',
          hover: 'hover:bg-red-700',
          ring: 'focus:ring-red-500',
          'ring-offset': 'focus:ring-offset-red-200',
          text: 'text-white'
        }
      } else if (this.color === 'gray') {
        return {
          main: 'bg-gray-600',
          hover: 'hover:bg-gray-700',
          ring: 'focus:ring-gray-500',
          'ring-offset': 'focus:ring-offset-gray-200',
          text: 'text-white'
        }
      } else if (this.color === 'light-gray') {
        return {
          main: 'bg-gray-50 border border-gray-300',
          hover: 'hover:bg-gray-100',
          ring: 'focus:ring-gray-500',
          'ring-offset': 'focus:ring-offset-gray-300',
          text: 'text-gray-700'
        }
      } else if (this.color === 'green') {
        return {
          main: 'bg-green-600',
          hover: 'hover:bg-green-700',
          ring: 'focus:ring-green-500',
          'ring-offset': 'focus:ring-offset-green-200',
          text: 'text-white'
        }
      } else if (this.color === 'yellow') {
        return {
          main: 'bg-yellow-600',
          hover: 'hover:bg-yellow-700',
          ring: 'focus:ring-yellow-500',
          'ring-offset': 'focus:ring-offset-yellow-200',
          text: 'text-white'
        }
      } else if (this.color === 'white') {
        return {
          main: 'bg-transparent border border-gray-300',
          hover: 'hover:bg-gray-200',
          ring: 'focus:ring-white-500',
          'ring-offset': 'focus:ring-offset-white-200',
          text: 'text-gray-700'
        }
      }
      console.error('Unknown color')
    },
    sizes () {
      if (this.size === 'small') {
        return {
          font: 'sm',
          'p-y': 'py-1',
          'p-x': 'px-2'
        }
      }
      return {
        font: 'base',
        'p-y': 'py-2',
        'p-x': 'px-4'
      }
    }
  },
}
</script>
