<template>
    <div
        role="radio"
        :aria-checked="selected"
        :class="[
          theme.FlatSelectInput.spacing.vertical,
          theme.FlatSelectInput.fontSize,
          theme.FlatSelectInput.option,
        ]"
        @click="onSelect"
    >
        <Icon
            v-if="selected"
            name="material-symbols:radio-button-checked-outline"
            class="text-inherit"
            :color="color"
            :class="[theme.FlatSelectInput.icon]"
        />
        <Icon
            v-else
            name="material-symbols:radio-button-unchecked"
            :class="[theme.FlatSelectInput.icon,theme.FlatSelectInput.unselectedIcon]"
        />
    </div>
</template>
<script>
export default {
    emits: ['toggle-select'],
    props: {
        theme: { type: Object },
        color: {type: String, default: "#3B82F6"},
        selected: { type: Boolean, default: false }
    },
    methods: {
        onSelect(){
            this.$emit("toggle-select")
        }
    }

}

</script>