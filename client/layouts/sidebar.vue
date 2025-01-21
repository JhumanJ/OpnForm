<template>
  <main class="flex h-full">
    <sub-sidebar
      :class="[tabsList.length ? 'w-[72px]' : 'w-[219px]']"
      :tabs-list="tabsMenuList"
    />

    <sub-sidebar
      v-if="tabsList.length"
      :is-sub-sidebar="!!tabsList.length"
      class="w-[219px]"
      :tabs-list="tabsList"
    />
  </main>
</template>

<script setup lang="ts">
import SubSidebar from "~/components/global/SubSidebar.vue"
import { computed } from "vue"
import opnformConfig from "~/opnform.config"

const workspacesStore = useWorkspacesStore()
const slug = useRoute().params.slug
const workspace = computed(() => workspacesStore.getCurrent)

const tabsMenuList = [
  {
    name: "Dashboard",
    route: "home",
    params: { slug },
    iconPath: "/img/sidebar/dashboard.svg",
  },

  {
    name: "Templates",
    route: "templates-my-templates",
    params: { slug },
    iconPath: "/img/sidebar/templates.svg",
  },
  // {
  //   name: "Help Center",
  //   route: opnformConfig.links.help_url,
  //   params: { slug },
  //   iconPath: "/img/sidebar/help-center.svg",
  // },
  {
    name: "Settings",
    route: "settings-profile",
    params: { slug },
    iconPath: "/img/sidebar/settings.svg",
  },
]

const tabsList = [
  {
    name: "Submissions",
    route: "forms-slug-show-submissions",
    params: { slug },
    iconPath: "/img/sidebar/submissions.svg",
  },
  ...(workspace.value.is_readonly
    ? []
    : [
        {
          name: "Integrations",
          route: "forms-slug-show-integrations",
          params: { slug },
          iconPath: "/img/sidebar/integrations.svg",
        },
      ]),
  {
    name: "Analytics",
    route: "forms-slug-show-stats",
    params: { slug },
    iconPath: "/img/sidebar/analytics.svg",
  },
  {
    name: "Share",
    route: "forms-slug-show-share",
    params: { slug },
    iconPath: "/img/sidebar/share.svg",
  },
]
</script>
