<template>
  <div ref="parent"
       tabindex="0"
       :class="{
         'hover:bg-gray-100 dark:hover:bg-gray-800 rounded px-2 cursor-pointer': !editing
         }"

       class="relative"
       :style="{height: editing?(divHeight+'px'):'auto'}"
       @focus="startEditing"
  >
    <slot v-if="!editing" :content="content">
      <label class="cursor-pointer truncate w-full">
        {{ content }}
      </label>
    </slot>
    <div v-if="editing" class="absolute inset-0 border-2 transition-colors"
         :class="{'border-transparent':!editing,'border-blue-500':editing}">
      <input ref="editinput" v-model="content"
             class="absolute inset-0 focus:outline-none bg-white transition-colors"
             :class="[{'bg-blue-50':editing},contentClass]" @blur="editing = false" @keyup.enter="editing = false"
             @input="handleInput"
      >
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {required: true},
    textAlign: {type: String, default: 'left'},
    contentClass: {type: String | Object, default: ''}
  },

  data() {
    return {
      content: this.value,
      editing: false,
      divHeight: 0
    }
  },

  methods: {
    startEditing() {
      this.divHeight = this.$refs.parent.offsetHeight
      this.editing = true
      this.$nextTick(() => {
        this.$refs.editinput.focus()
      })
    },
    handleInput(e) {
      this.$emit('input', this.content)
    }
  }
}
</script>
