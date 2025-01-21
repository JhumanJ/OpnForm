<template>
  <div class="bg-[#F5F7F9] w-full">
    <template v-if="form">
      <div class="p-6 flex flex-col gap-[20px]">
        <div class="flex items-end justify-between">
          <div class="flex flex-col gap-[6px]">
            <h3 class="font-medium text-[22px]">
              Share
            </h3>
            <p>Manage your links to share it with others</p>
          </div>
          <div class="flex gap-3 items-center">
            <regenerate-form-link
              v-if="!workspace.is_readonly"
              class="sm:w-1/2 flex !w-full"
              :form="form"
            />
            <extra-menu
              v-if="!workspace.is_readonly"
              :form="form"
            />
          </div>
        </div>
        <div class="flex flex-col">
          <NuxtPage :form="form" />
        </div>
      </div>
      <!--      <div class="flex bg-gray-50">-->
      <!--        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">-->
      <!--          <div class="pt-4 pb-0">-->
      <!--            <a-->
      <!--              href="#"-->
      <!--              class="flex text-blue mb-2 font-semibold text-sm"-->
      <!--              @click.prevent="goBack"-->
      <!--            >-->
      <!--              <svg-->
      <!--                class="w-3 h-3 text-blue mt-1 mr-1"-->
      <!--                viewBox="0 0 6 10"-->
      <!--                fill="none"-->
      <!--                xmlns="http://www.w3.org/2000/svg"-->
      <!--              >-->
      <!--                <path-->
      <!--                  d="M5 9L1 5L5 1"-->
      <!--                  stroke="currentColor"-->
      <!--                  stroke-width="1.5"-->
      <!--                  stroke-linecap="round"-->
      <!--                  stroke-linejoin="round"-->
      <!--                />-->
      <!--              </svg>-->
      <!--              Go back-->
      <!--            </a>-->

      <!--            <div class="flex flex-wrap">-->
      <!--              <h2 class="flex-grow text-gray-900 truncate">-->
      <!--                {{ form.title }}-->
      <!--              </h2>-->
      <!--              <div class="flex mt-4 gap-2 lg:mt-0">-->
      <!--                <UButton-->
      <!--                  v-if="form.visibility === 'draft'"-->
      <!--                  color="white"-->
      <!--                  class="hover:no-underline"-->
      <!--                  icon="i-heroicons-eye"-->
      <!--                  @click="showDraftFormWarningNotification"-->
      <!--                >-->
      <!--                  <span class="hidden sm:inline"-->
      <!--                    >View <span class="hidden md:inline">form</span></span-->
      <!--                  >-->
      <!--                </UButton>-->
      <!--                <UButton-->
      <!--                  v-else-->
      <!--                  v-track.view_form_click="{-->
      <!--                    form_id: form.id,-->
      <!--                    form_slug: form.slug,-->
      <!--                  }"-->
      <!--                  target="_blank"-->
      <!--                  :to="form.share_url"-->
      <!--                  color="white"-->
      <!--                  class="hover:no-underline"-->
      <!--                  icon="i-heroicons-eye"-->
      <!--                >-->
      <!--                  <span class="hidden sm:inline"-->
      <!--                    >View <span class="hidden md:inline">form</span></span-->
      <!--                  >-->
      <!--                </UButton>-->
      <!--                <UButton-->
      <!--                  v-if="!workspace.is_readonly"-->
      <!--                  v-track.edit_form_click="{-->
      <!--                    form_id: form.id,-->
      <!--                    form_slug: form.slug,-->
      <!--                  }"-->
      <!--                  color="primary"-->
      <!--                  icon="i-heroicons-pencil"-->
      <!--                  class="hover:no-underline"-->
      <!--                  :to="{ name: 'forms-slug-edit', params: { slug: form.slug } }"-->
      <!--                >-->
      <!--                  Edit <span class="hidden md:inline">form</span>-->
      <!--                </UButton>-->
      <!--                <extra-menu v-if="!workspace.is_readonly" :form="form" />-->
      <!--              </div>-->
      <!--            </div>-->

      <!--            <p class="text-gray-500 text-sm">-->
      <!--              <span class="pr-1"-->
      <!--                >{{ form.views_count }} view{{-->
      <!--                  form.views_count > 0 ? "s" : ""-->
      <!--                }}</span-->
      <!--              >-->
      <!--              <span class="pr-1"-->
      <!--                >- {{ form.submissions_count }} submission{{-->
      <!--                  form.submissions_count > 0 ? "s" : ""-->
      <!--                }}-->
      <!--              </span>-->
      <!--              <span>- Edited {{ form.last_edited_human }}</span>-->
      <!--            </p>-->
      <!--            <div-->
      <!--              v-if="-->
      <!--                ['draft', 'closed'].includes(form.visibility) ||-->
      <!--                (form.tags && form.tags.length > 0)-->
      <!--              "-->
      <!--              class="mt-2 flex items-center flex-wrap gap-3"-->
      <!--            >-->
      <!--              <span-->
      <!--                v-if="form.visibility == 'draft'"-->
      <!--                class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700"-->
      <!--              >-->
      <!--                Draft - not publicly accessible-->
      <!--              </span>-->
      <!--              <span-->
      <!--                v-else-if="form.visibility == 'closed'"-->
      <!--                class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700"-->
      <!--              >-->
      <!--                Closed - won't accept new submissions-->
      <!--              </span>-->
      <!--              <span-->
      <!--                v-for="tag in form.tags"-->
      <!--                :key="tag"-->
      <!--                class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700"-->
      <!--              >-->
      <!--                {{ tag }}-->
      <!--              </span>-->
      <!--            </div>-->

      <!--            <p v-if="form.closes_at" class="text-yellow-500">-->
      <!--              <span v-if="form.is_closed">-->
      <!--                This form stopped accepting submissions on the-->
      <!--                {{ displayClosesDate }}-->
      <!--              </span>-->
      <!--              <span v-else>-->
      <!--                This form will stop accepting submissions on the-->
      <!--                {{ displayClosesDate }}-->
      <!--              </span>-->
      <!--            </p>-->
      <!--            <p v-if="form.max_submissions_count > 0" class="text-yellow-500">-->
      <!--              <span v-if="form.max_number_of_submissions_reached">-->
      <!--                The form is now closed because it reached its limit of-->
      <!--                {{ form.max_submissions_count }} submissions.-->
      <!--              </span>-->
      <!--              <span v-else>-->
      <!--                This form will stop accepting submissions after-->
      <!--                {{ form.max_submissions_count }} submissions.-->
      <!--              </span>-->
      <!--            </p>-->

      <!--            <form-cleanings class="mt-4" :form="form" />-->

      <!--            <div class="border-b border-gray-200 dark:border-gray-700">-->
      <!--              <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">-->
      <!--                <li v-for="(tab, i) in tabsList" :key="i + 1" class="mr-6">-->
      <!--                  <nuxt-link-->
      <!--                    :to="{ name: tab.route, params: tab.params ?? {} }"-->
      <!--                    class="hover:no-underline inline-block py-4 rounded-t-lg border-b-2 text-gray-500 hover:text-gray-600"-->
      <!--                    active-class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500"-->
      <!--                  >-->
      <!--                    {{ tab.name }}-->
      <!--                  </nuxt-link>-->
      <!--                </li>-->
      <!--              </ul>-->
      <!--            </div>-->
      <!--          </div>-->
      <!--        </div>-->
      <!--      </div>-->
      <!--      <div class="flex flex-col bg-white">-->
      <!--        <NuxtPage :form="form" />-->
      <!--      </div>-->
    </template>
    <div
      v-else-if="loading"
      class="text-center w-full p-5"
    >
      <Loader class="h-6 w-6 mx-auto" />
    </div>
    <div
      v-else
      class="text-center w-full p-5"
    >
      Form not found.
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
import ExtraMenu from "../../../components/pages/forms/show/ExtraMenu.vue"
import FormCleanings from "../../../components/pages/forms/show/FormCleanings.vue"
import RegenerateFormLink from "~/components/pages/forms/show/RegenerateFormLink.vue"

definePageMeta({
  middleware: "auth",
})
useOpnSeoMeta({
  title: "Home",
})

const route = useRoute()
const formsStore = useFormsStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()

const slug = useRoute().params.slug

formsStore.startLoading()
const form = computed(() => formsStore.getByKey(slug))
const workspace = computed(() => workspacesStore.getCurrent)

const loading = computed(() => formsStore.loading || workspacesStore.loading)
const displayClosesDate = computed(() => {
  if (form.value && form.value.closes_at) {
    const dateObj = new Date(form.value.closes_at)
    return (
      dateObj.getFullYear() +
      "-" +
      String(dateObj.getMonth() + 1).padStart(2, "0") +
      "-" +
      String(dateObj.getDate()).padStart(2, "0") +
      " " +
      String(dateObj.getHours()).padStart(2, "0") +
      ":" +
      String(dateObj.getMinutes()).padStart(2, "0")
    )
  }
  return ""
})

const tabsList = [
  {
    name: "Submissions",
    route: "forms-slug-show-submissions",
    params: { slug: slug },
  },
  ...(workspace.value.is_readonly
    ? []
    : [
        {
          name: "Integrations",
          route: "forms-slug-show-integrations",
          params: { slug: slug },
        },
      ]),
  {
    name: "Analytics",
    route: "forms-slug-show-stats",
    params: { slug: slug },
  },
  {
    name: "Share",
    route: "forms-slug-show-share",
    params: { slug: slug },
  },
]

onMounted(() => {
  workingFormStore.reset()
  if (form.value) {
    workingFormStore.set(form.value)
  } else {
    formsStore.loadForm(route.params.slug)
  }
})

watch(
  () => form?.value?.id,
  (id) => {
    if (id) {
      workingFormStore.set(form.value)
    }
  },
)

const goBack = () => {
  useRouter().push({ name: "home" })
}

const showDraftFormWarningNotification = () => {
  useAlert().warning(
    "This form is currently in Draft mode and is not publicly accessible, You can change the form status on the edit form page.",
  )
}
</script>
