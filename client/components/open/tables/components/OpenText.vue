<template>
  <span v-if="!valueIsObject">
    {{ value }}
  </span>
  <span v-else>
    <span
      v-for="(item, i) in value.responseData"
      :key="i"
      :class="{
        'font-semibold': item.annotations.bold && !item.annotations.code,
        italic: item.annotations.italic,
        'line-through': item.annotations.strikethrough,
        underline: item.annotations.underline,
        'bg-pink-100 py-1 px-2 rounded-lg text-pink-500': item.annotations.code,
        'font-serif': item.type == 'equation',
      }"
      :style="{
        color:
          item.annotations.color != 'default'
            ? getColor(item.annotations.color)
            : null,
        'background-color':
          item.annotations.color != 'default' &&
          item.annotations.color.split('_')[1]
            ? getBgColor(item.annotations.color.split('_')[0])
            : 'none',
      }"
    >
      <a
        v-if="item.href"
        :href="item.href"
        rel="noopener noreferrer"
        target="_blank"
        class="text-blue-600 underline"
      >{{ item.plain_text }}</a>
      <span v-else-if="!item.href">{{ item.plain_text }}</span>
    </span>
  </span>
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      required: true
    }

  },

  data () {
    return {}
  },

  computed: {
    valueIsObject () {
      if (
        typeof this.value === 'object' &&
        !Array.isArray(this.value) &&
        this.value !== null
      ) {
        return true
      }
      return false
    }
  },
  mounted () {
  },

  methods: {
    getColor (color) {
      return {
        red: '#e03e3e',
        gray: '#9b9a97',
        brown: '#64473a',
        orange: '#d9730d',
        yellow: '#dfab01',
        teal: '#0f7b6c',
        blue: '#0b6e99',
        purple: '#6940a5',
        pink: '#ad1a72'
      }[color]
    },
    getBgColor (color) {
      return {
        red: '#fbe4e4',
        gray: '#ebeced',
        brown: '#e9e5e3',
        orange: '#faebdd',
        yellow: '#fbf3db',
        teal: '#ddedea',
        blue: '#ddebf1',
        purple: '#eae4f2',
        pink: '#f4dfeb'
      }[color]
    }
  }
}
</script>
