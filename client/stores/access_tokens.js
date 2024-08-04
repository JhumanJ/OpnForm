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
      title: 'List forms',
      name: 'list-forms',
    },
    {
      title: 'List workspaces',
      name: 'list-workspaces',
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
    return abilities.find(ability => ability.name == name)
  }

  return {
    ...contentStore,
    fetchTokens,
    tokens,
    abilities,
    getAbility
  }
})
