<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>
    <div class="flex space-x-2">
      <div class="flex-1">
        <input
          type="range"
          class="w-full"
          :min="minSlider"
          :max="maxSlider"
          :step="stepSlider"
          v-model="compVal"
        />
        <div class="grid grid-cols-3 gap-2">
          <div
            v-for="i in sliderLabelsList"
            :key="i"
            :class="[
              theme.SliderInput.stepLabel,
              i.style,
            ]"
          >
            {{ i.label }}
          </div>
        </div>
      </div>
      <span class="w-8 font-semibold text-lg px-2">{{ compVal }}</span>
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from "./useFormInput.js";
import InputWrapper from "./components/InputWrapper.vue";

export default {
  name: "SliderInput",
  components: { InputWrapper },

  props: {
    ...inputProps,
    minSlider: { type: Number, default: 0 },
    maxSlider: { type: Number, default: 50 },
    stepSlider: { type: Number, default: 5 },
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    };
  },
  computed: {
    sliderLabelsList() {
      const midPoint = (this.maxSlider - this.minSlider) / 2 + this.minSlider;
      const labels = [
        {
          label: `Min(${this.minSlider})`,
          style: "flex items-center justify-start",
        },
        {
          label: Math.floor(midPoint),
          style: "flex items-center justify-center",
        },
        {
          label: `Max(${this.maxSlider})`,
          style: "flex items-center justify-end",
        },
      ];
      return labels;
    },
  },
  mounted() {
    this.compVal = parseInt(this.compVal ?? this.minSlider);
  },

 
};
</script>
