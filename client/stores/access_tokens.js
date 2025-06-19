import { defineStore } from "pinia"
import { useContentStore } from "~/composables/stores/useContentStore.js"

export const useAccessTokenStore = defineStore("access_tokens", () => {
  const contentStore = useContentStore()

  const abilities = [
    {
      title: 'Manage integrations',
      name: 'manage-integrations',
    },
    {
      title: 'Forms – Read',
      name: 'forms-read',
    },
    {
      title: 'Forms – Write',
      name: 'forms-write',
    },
    {
      title: 'Workspaces – Read',
      name: 'workspaces-read',
    },
    {
      title: 'Workspaces – Write',
      name: 'workspaces-write',
    },
    {
      title: 'Workspace Users – Read',
      name: 'workspace-users-read',
    },
    {
      title: 'Workspace Users – Write',
      name: 'workspace-users-write',
    },
  ]

  const fetchTokens = () => {
    contentStore.resetState()
    contentStore.startLoading()

    return opnFetch('/settings/tokens').then(
      (data) => {
        contentStore.save(data)
        contentStore.stopLoading()
      },
    )
  }

  const tokens = computed(() => contentStore.getAll.value)

  const getAbility = (name) => {
    return abilities.find((ability) => ability.name === name) ?? {
      name,
      title: name,
    }
  }

  return {
    ...contentStore,
    fetchTokens,
    tokens,
    abilities,
    getAbility
  }
})
