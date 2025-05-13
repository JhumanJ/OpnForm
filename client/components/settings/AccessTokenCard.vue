<template>
  <div
    class="text-gray-500 border shadow rounded-md p-5 mt-4 relative flex items-center"
  >
    <div class="flex-grow flex items-center">
      <div>
        <div class="flex space-x-3 font-semibold mr-2">
          {{ token.name }}
        </div>

        <div class="">
          <span
            v-for="(ability, index) in token.abilities"
            :key="index"
          >
            {{ accessTokenStore.getAbility(ability).title }}
            <template v-if="index !== token.abilities.length - 1">
              ,&nbsp;
            </template>
          </span>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <dropdown
        class="inline"
      >
        <template #trigger="{ toggle }">
          <v-button
            color="white"
            @click="toggle"
          >
            <Icon
              name="heroicons:ellipsis-horizontal"
              class="w-4 h-4 -mt-1"
            />
          </v-button>
        </template>

        <a
          href="#"
          class="flex px-4 py-2 text-md text-red-600 hover:bg-red-50 hover:no-underline items-center"
          @click.prevent="destroy"
        >
          <Icon
            name="heroicons:trash"
            class="w-5 h-5 mr-2"
          />

          Delete
        </a>
      </dropdown>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  token: Object
})

const accessTokenStore = useAccessTokenStore()
const alert = useAlert()

function destroy() {
  alert.confirm("Do you really want to delete this token?", () => {
  opnFetch(`/settings/tokens/${props.token.id}`, {
    method: 'DELETE'
  })
    .then(() => {
      accessTokenStore.remove(props.token.id)
    })
    .catch((error) => {
      try {
        alert.error(error.data.message)
      } catch {
        alert.error("An error occurred while disconnecting an account")
      }
    })
  })
}
</script>
