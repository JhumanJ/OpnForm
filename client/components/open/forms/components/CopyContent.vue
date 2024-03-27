<template>
  <div class="flex flex-wrap sm:flex-nowrap my-4 w-full">
    <div class="w-full sm:w-auto border border-gray-300 rounded-md p-2 flex-grow select-all bg-gray-100">
      <p class="select-all text-gray-900">
        {{ content }}
      </p>
    </div>
    <div class="w-full sm:w-40 sm:ml-2 mt-2 sm:mt-0 shrink-0">
      <v-button color="light-gray" class="w-full" @click="copyToClipboard">
        <slot name="icon">
          <svg class="h-4 w-4 -mt-1 text-blue-600 inline mr-1" viewBox="0 0 20 20" fill="none"
               xmlns="http://www.w3.org/2000/svg">
            <path
              d="M4.16667 12.4998C3.3901 12.4998 3.00182 12.4998 2.69553 12.373C2.28715 12.2038 1.9627 11.8794 1.79354 11.471C1.66667 11.1647 1.66667 10.7764 1.66667 9.99984V4.33317C1.66667 3.39975 1.66667 2.93304 1.84833 2.57652C2.00812 2.26292 2.26308 2.00795 2.57669 1.84816C2.93321 1.6665 3.39992 1.6665 4.33334 1.6665H10C10.7766 1.6665 11.1649 1.6665 11.4711 1.79337C11.8795 1.96253 12.204 2.28698 12.3731 2.69536C12.5 3.00165 12.5 3.38993 12.5 4.1665M10.1667 18.3332H15.6667C16.6001 18.3332 17.0668 18.3332 17.4233 18.1515C17.7369 17.9917 17.9919 17.7368 18.1517 17.4232C18.3333 17.0666 18.3333 16.5999 18.3333 15.6665V10.1665C18.3333 9.23308 18.3333 8.76637 18.1517 8.40985C17.9919 8.09625 17.7369 7.84128 17.4233 7.68149C17.0668 7.49984 16.6001 7.49984 15.6667 7.49984H10.1667C9.23325 7.49984 8.76654 7.49984 8.41002 7.68149C8.09642 7.84128 7.84145 8.09625 7.68166 8.40985C7.50001 8.76637 7.50001 9.23308 7.50001 10.1665V15.6665C7.50001 16.5999 7.50001 17.0666 7.68166 17.4232C7.84145 17.7368 8.09642 17.9917 8.41002 18.1515C8.76654 18.3332 9.23325 18.3332 10.1667 18.3332Z"
              stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </slot>
        <slot></slot>
      </v-button>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const { copy } = useClipboard()

const props = defineProps({
  content: {
    type: String,
    required: true
  },
  isDraft: {
    type: Boolean,
    default: false
  }
})

const copyToClipboard = () => {
  if (import.meta.server) return
  copy(props.content)
  if(props.isDraft){
    useAlert().warning('Copied! But other people won\'t be able to see the form since it\'s currently in draft mode')
  } else {
    useAlert().success('Copied!')
  }
}
</script>
