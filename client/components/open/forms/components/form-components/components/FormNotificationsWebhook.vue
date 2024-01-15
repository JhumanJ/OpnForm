<template>
  <div>
    <button
      class="flex items-center mt-3 cursor-pointer relative w-full rounded-lg flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click.prevent="showModal=true"
    >
      <div class="flex-grow flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 inline"
        >
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"
          />
        </svg>
        <p class="flex-grow text-center">
          Webhook Notifications
        </p>
      </div>
      <div v-if="form.notifies_webhook">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 text-nt-blue"
        >
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
      </div>
    </button>
    <modal :show="showModal" @close="showModal=false">
      <h2 class="text-2xl font-bold z-10 truncate mb-5 text-nt-blue">
        Webhook Notifications
        <pro-tag />
      </h2>
      <toggle-switch-input name="notifies_webhook" :form="form" class="mt-4"
                           label="Trigger a webhook notification on form submission"
                           @change="onToggleChange"
      />
      <text-input v-if="form.notifies_webhook" name="webhook_url" :form="form" class="mt-4"
                  label="Webhook url" help="We will post form submissions to this endpoint"
      />
    </modal>
  </div>
</template>

<script>
import { useWorkingFormStore } from '../../../../../../stores/working_form'
import ProTag from '~/components/global/ProTag.vue'

export default {
  components: { ProTag },
  props: {},
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore
    }
  },
  data () {
    return {
      showModal: false
    }
  },

  computed: {
    form: {
      get () {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set (value) {
        this.workingFormStore.set(value)
      }
    }
  },

  watch: {},

  mounted () {
  },

  methods: {
    onToggleChange () {
      if (!this.form.notifies_webhook) {
        this.form.webhook_url = ''
      }
    }
  }
}
</script>
