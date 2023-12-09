<template>
  <span :id="opt"
        class="py-1 px-2 mb-1 open-tag default mr-2 text-gray-700 dark:text-gray-300 text-xs whitespace-nowrap rounded-md transition-colors"
  >
    {{ opt }}
  </span>
</template>

<script>
export default {
  components: {},
  props: {
    opt: {
      type: String,
      required: true
    }
  },

  data () {
    return {}
  },

  computed: {
  },
  mounted () {
  },

  methods: {}
}
</script>

<style lang="scss">
.open-tag {
  display: inline-block;
  &.gray {
    background-color: #e3e2e0;
  }
  &.light-gray,&.default {
    background-color: #e3e2e080;
  }
  &.brown {
    background-color: #eee0da;
  }
  &.orange {
    background-color: #fadec9;
  }
  &.yellow {
    background-color: #fdecc8;
  }
  &.green {
    background-color: #dbeddb;
  }
  &.blue {
    background-color: #d3e5ef;
  }
  &.purple {
    background-color: #e8deee;
  }
  &.pink {
    background-color: #f5e0e9;
  }
  &.red {
    background-color: #ffe2dd;
  }
}

.dark {
  .open-tag {
    &.gray {
      background-color: #5a5a5a;
    }
    &.light-gray,&.default {
      background-color: #ffffff21;
    }
    &.brown {
      background-color: #603b2c;
    }
    &.orange {
      background-color: #854c1d;
    }
    &.yellow {
      background-color: #89632a;
    }
    &.green {
      background-color: #2b593f;
    }
    &.blue {
      background-color: #28456c;
    }
    &.purple {
      background-color: #492f64;
    }
    &.pink {
      background-color: #69314c;
    }
    &.red {
      background-color: #6e3630;
    }
  }
}
</style>