<template>
  <div>
    <button
      class="flex items-center mt-3 cursor-pointer relative w-full rounded-lg flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click.prevent="showModal=true">
      <div class="flex-grow flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-5 h-5 inline">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
        </svg>
        <p class="flex-grow text-center">
          Email Thông báo
        </p>
      </div>
      <div v-if="form.notifies">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-5 h-5 text-nt-blue">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
    </button>
    <modal :show="showModal" @close="showModal=false">
      <h2 class="text-2xl font-bold z-10 truncate mb-5 text-nt-blue">
        Thông báo biểu mẫu
        <pro-tag />
      </h2>
      <toggle-switch-input name="notifies" :form="form" class="mt-4" label="Nhận thông báo qua email khi gửi" />
      <template v-if="form.notifies">
        <text-input name="notification_reply_to" v-model="form.notification_settings.notification_reply_to" class="mt-4"
          label="Trả lời thông báo tới" :help="notifiesHelp" />
        <text-area-input name="notification_emails" :form="form" class="mt-4" label="Email thông báo"
          help="Add one email per line" />
      </template>
    </modal>
  </div>
</template>

<script>
import ProTag from '../../../../../common/ProTag.vue'

export default {
  components: { ProTag },
  props: {},
  data () {
    return {
      showModal: false
    }
  },

  computed: {
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    replayToEmailField () {
      const emailFields = this.form.properties.filter((field) => {
        return field.type === 'email' && !field.hidden
      })
      if (emailFields.length === 1) return emailFields[0]
      return null
    },
    notifiesHelp() {
      if (this.replayToEmailField) {
        return 'Nếu trống, Reply-to cho thông báo này sẽ là email được điền trong trường "' + this.replayToEmailField.name + '".';
      }
      return 'Nếu trống, Reply-to cho thông báo này sẽ là email của bạn. Thêm một trường email duy nhất vào biểu mẫu của bạn và nó sẽ tự động trở thành giá trị reply to.';
    }
  },

  watch: {},

  mounted () {
  },

  methods: {}
}
</script>
