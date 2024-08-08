module.exports = {
  operation: {
    perform: {
      headers: { Accept: 'application/json' },
      params: {
        api_key: '{{bundle.authData.api_key}}',
        workspace_id: '{{bundle.inputData.workspace_id}}',
      },
      removeMissingValuesFrom: { body: false, params: false },
      url: '{{process.env.BASE_URL}}/external/zapier/forms',
    },
    inputFields: [
      {
        key: 'workspace_id',
        type: 'string',
        dynamic: 'list_workspaces.id.name',
        label: 'Workspace',
        required: true,
        list: false,
        altersDynamicFields: false,
      },
    ],
    sample: { id: 'my-form', name: 'My Form' },
    outputFields: [
      { key: 'id', label: 'ID', type: 'string' },
      { key: 'name', label: 'Name', type: 'string' },
    ],
    canPaginate: false,
  },
  display: {
    description: 'Get the list of all forms',
    hidden: true,
    label: 'List Forms',
  },
  key: 'list_forms',
  noun: 'Form',
};
