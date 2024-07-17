module.exports = {
  operation: {
    perform: {
      headers: { Accept: 'application/json' },
      removeMissingValuesFrom: { body: false, params: false },
      url: '{{process.env.BASE_URL}}/api/zapier/workspaces',
    },
    sample: { id: 1, name: 'My Workspace' },
    outputFields: [
      { key: 'id', label: 'ID', type: 'integer' },
      { key: 'name', label: 'Name', type: 'string' },
    ],
  },
  display: {
    description: "Get the list of all user's workspaces",
    hidden: true,
    label: 'List Workspaces',
  },
  key: 'list_workspaces',
  noun: 'Workspace',
};
