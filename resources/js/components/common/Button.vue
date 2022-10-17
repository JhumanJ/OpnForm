<template>
  <button :type="nativeType" :disabled="loading" :class="`py-${sizes['p-y']} px-${sizes['p-x']}
    bg-${color}-${colorShades['main']} hover:bg-${color}-${colorShades['hover']} focus:ring-${color}-${colorShades['ring']}
    focus:ring-offset-${color}-${colorShades['ring-offset']} text-${colorShades['text']}
    transition ease-in duration-200 text-center text-${sizes['font']} font-semibold focus:outline-none focus:ring-2
    focus:ring-offset-2 border-2 border-${colorShades['border']} rounded-lg`"
          @click="$emit('click',$event)"
  >
    <template v-if="!loading">
      <slot />
      <svg v-if="arrow" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="float-right mt-1 ml-2 w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
      </svg>
    </template>
    <loader v-else class="h-6 w-6 mx-auto" :class="`text-${colorShades['text']}`" />
  </button>
</template>

<script>
export default {
  name: 'VButton',

  props: {
    color: {
      type: String,
      default: 'blue'
    },

    shade: {
      type: String,
      default: 'normal'
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
    }
  },

  computed: {
    colorShades () {
      if (this.shade === 'lighter') {
        return {
          main: '200',
          hover: '300',
          ring: '100',
          'ring-offset': '50',
          text: 'gray-900',
          border: 'blue'
        }
      }
      if (this.shade === 'light') {
        return {
          main: '400',
          hover: '500',
          ring: '300',
          'ring-offset': '150',
          text: 'white',
          border: 'white'
        }
      }
      return {
        main: '600',
        hover: '700',
        ring: '500',
        'ring-offset': '200',
        text: 'white',
        border: 'blue'
      }
    },
    sizes () {
      if (this.size === 'small') {
        return {
          font: 'sm',
          'p-y': '1',
          'p-x': '2'
        }
      }
      return {
        font: 'base',
        'p-y': '2',
        'p-x': '4'
      }
    }
  }
}
</script>
