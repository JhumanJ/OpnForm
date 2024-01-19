<template>
  <div class="space-y-2 py-4">
    <p>
      This form is currently in
      <span
        class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700"
      >
        Draft
      </span>
      and is not publicly accessible.
    </p>
    <p>You can change the form status on the edit form page.</p>
    <v-button
      class="text-white"
      :to="to"
    >
      <svg
        class="inline mr-1 -mt-1"
        width="18"
        height="17"
        viewBox="0 0 18 17"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M8.99998 15.6662H16.5M1.5 15.6662H2.89545C3.3031 15.6662 3.50693 15.6662 3.69874 15.6202C3.8688 15.5793 4.03138 15.512 4.1805 15.4206C4.34869 15.3175 4.49282 15.1734 4.78107 14.8852L15.25 4.4162C15.9404 3.72585 15.9404 2.60656 15.25 1.9162C14.5597 1.22585 13.4404 1.22585 12.75 1.9162L2.28105 12.3852C1.9928 12.6734 1.84867 12.8175 1.7456 12.9857C1.65422 13.1348 1.58688 13.2974 1.54605 13.4675C1.5 13.6593 1.5 13.8631 1.5 14.2708V15.6662Z"
          stroke="currentColor"
          stroke-width="1.67"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
      Edit form
    </v-button>
  </div>
</template>

<script>
export default {
  name: "DraftFormPreviewWarning",
  props: {
    to: {
      type: Object,
      default: null,
    },
  },
};
</script>