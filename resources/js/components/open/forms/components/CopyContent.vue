<template>
    <div class="flex my-4 w-full">
        <div class="border border-gray-300 bg-white dark:bg-gray rounded-md p-2 w-4/5 select-all">
            <p class="select-all text-gray-900">
                {{ content }}
            </p>
        </div>
        <v-button class="ml-2" size="small" color="light-gray" @click="copyToClipboard(content)">
            <svg v-if="buttonIcon=='link'" class="h-4 w-4 -mt-1 text-blue-600 inline mr-1" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.49984 9.16634H5.83317C3.53198 9.16634 1.6665 7.30086 1.6665 4.99967C1.6665 2.69849 3.53198 0.833008 5.83317 0.833008H7.49984M12.4998 9.16634H14.1665C16.4677 9.16634 18.3332 7.30086 18.3332 4.99967C18.3332 2.69849 16.4677 0.833008 14.1665 0.833008H12.4998M5.83317 4.99967L14.1665 4.99968" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg v-else-if="buttonIcon=='code'" class="h-4 w-4 text-blue-600 inline mr-1" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.0833 11.5L13.5833 9L11.0833 6.5M6.91667 6.5L4.41667 9L6.91667 11.5M5.5 16.5H12.5C13.9001 16.5 14.6002 16.5 15.135 16.2275C15.6054 15.9878 15.9878 15.6054 16.2275 15.135C16.5 14.6002 16.5 13.9001 16.5 12.5V5.5C16.5 4.09987 16.5 3.3998 16.2275 2.86502C15.9878 2.39462 15.6054 2.01217 15.135 1.77248C14.6002 1.5 13.9001 1.5 12.5 1.5H5.5C4.09987 1.5 3.3998 1.5 2.86502 1.77248C2.39462 2.01217 2.01217 2.39462 1.77248 2.86502C1.5 3.3998 1.5 4.09987 1.5 5.5V12.5C1.5 13.9001 1.5 14.6002 1.77248 15.135C2.01217 15.6054 2.39462 15.9878 2.86502 16.2275C3.3998 16.5 4.09987 16.5 5.5 16.5Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg v-else class="h-4 w-4 -mt-1 text-blue-600 inline mr-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.16667 12.4998C3.3901 12.4998 3.00182 12.4998 2.69553 12.373C2.28715 12.2038 1.9627 11.8794 1.79354 11.471C1.66667 11.1647 1.66667 10.7764 1.66667 9.99984V4.33317C1.66667 3.39975 1.66667 2.93304 1.84833 2.57652C2.00812 2.26292 2.26308 2.00795 2.57669 1.84816C2.93321 1.6665 3.39992 1.6665 4.33334 1.6665H10C10.7766 1.6665 11.1649 1.6665 11.4711 1.79337C11.8795 1.96253 12.204 2.28698 12.3731 2.69536C12.5 3.00165 12.5 3.38993 12.5 4.1665M10.1667 18.3332H15.6667C16.6001 18.3332 17.0668 18.3332 17.4233 18.1515C17.7369 17.9917 17.9919 17.7368 18.1517 17.4232C18.3333 17.0666 18.3333 16.5999 18.3333 15.6665V10.1665C18.3333 9.23308 18.3333 8.76637 18.1517 8.40985C17.9919 8.09625 17.7369 7.84128 17.4233 7.68149C17.0668 7.49984 16.6001 7.49984 15.6667 7.49984H10.1667C9.23325 7.49984 8.76654 7.49984 8.41002 7.68149C8.09642 7.84128 7.84145 8.09625 7.68166 8.40985C7.50001 8.76637 7.50001 9.23308 7.50001 10.1665V15.6665C7.50001 16.5999 7.50001 17.0666 7.68166 17.4232C7.84145 17.7368 8.09642 17.9917 8.41002 18.1515C8.76654 18.3332 9.23325 18.3332 10.1667 18.3332Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg> {{buttonText}}
        </v-button>
    </div>
  </template>
  
  <script>
  export default {
    name: 'CopyContent',
    props: {
      content: {
        type: String,
        required: true
      },
      buttonIcon: {
        type: String,
        default: ''
      },
      buttonText: {
        type: Boolean,
        default: 'Copy'
      }
    },
  
    data () {
      return {
      }
    },
  
    computed: {
    },
  
    watch: {
    },
  
    mounted () {
    },
  
    methods: {
      copyToClipboard (str) {
        const el = document.createElement('textarea')
        el.value = str
        document.body.appendChild(el)
        el.select()
        document.execCommand('copy')
        document.body.removeChild(el)
      }
    }
  }
  </script>
  