<template>
  <button :type="nativeType" :disabled="loading" :class="`py-${sizes['p-y']} px-${sizes['p-x']}
    bg-${color}-${colorShades['main']} hover:bg-${color}-${colorShades['hover']} focus:ring-${color}-${colorShades['ring']}
    focus:ring-offset-${color}-${colorShades['ring-offset']} text-${colorShades['text']}
    transition ease-in duration-200 text-center text-${sizes['font']} font-semibold focus:outline-none focus:ring-2
    focus:ring-offset-2 border-2 border-${colorShades['border']} rounded-full `"
          class="btn" @click="$emit('click',$event)"
  >
    <template v-if="!loading">
      <slot />
      <svg v-if="arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="float-right ml-2 w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
      </svg>
    </template>
    <loader v-else class="h-6 w-6 text-white mx-auto" />
  </button>
</template>

<script>
export default {
  name: 'VButton',

  props: {
    color: {
      type: String,
      default: 'normal'
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
      default: 'submit'
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
      if (this.color === 'nt-blue') {
        return {
          main: 'default',
          hover: 'light',
          ring: 'light',
          'ring-offset': 'lighter',
          text: 'white',
          border: 'nt-blue'
        }
      }
      if (this.shade === 'lighter') {
        return {
          main: '200',
          hover: '300',
          ring: '100',
          'ring-offset': '50',
          text: 'gray-900',
          border: 'nt-blue'
        }
      }
      if (this.shade === 'light') {
        return {
          main: '400',
          hover: '500',
          ring: '300',
          'ring-offset': '150',
          text: 'white',
          border: 'nt-blue'
        }
      }
      return {
        main: '600',
        hover: '700',
        ring: '500',
        'ring-offset': '200',
        text: 'nt-blue',
        border: 'nt-blue'
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
