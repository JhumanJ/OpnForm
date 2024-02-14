<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>
    <div class="flex space-x-2">
      <div class="flex-1 relative">
        <div class="font-medium text-sm absolute -top-[6px]"
             :style="labelStyle"
        >
          <div class="">
            {{ compVal }}
          </div>
        </div>
        <input
          type="range"
          class="w-full mt-3"
          :disabled="disabled"
          :min="minSlider"
          :max="maxSlider"
          :step="stepSlider"
          v-model="compVal"
        />
        <div class="grid grid-cols-3 gap-2 -mt-1">
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
    labelStyle() {
      const ratio = ((this.compVal-this.minSlider) / (this.maxSlider-this.minSlider)) * 100
      return {
        left: `${ratio}%`,
        marginLeft: `-${ratio/100*15}px`
      }
    },
    sliderLabelsList() {
      const midPoint = (this.maxSlider - this.minSlider) / 2 + this.minSlider;
      const labels = [
        {
          label: `${this.minSlider}`,
          style: "flex items-center justify-start",
        },
        {
          label: Math.floor(midPoint),
          style: "flex items-center justify-center",
        },
        {
          label: `${this.maxSlider}`,
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
