const perform = async (z, bundle) => {
  return [bundle.cleanedRequest];
};

module.exports = {
  operation: {
    perform: perform,
    inputFields: [
      {
        key: 'workspace_id',
        type: 'string',
        label: 'Workspace',
        dynamic: 'list_workspaces.id.name',
        required: true,
        list: false,
        altersDynamicFields: true,
      },
      {
        key: 'form_id',
        type: 'string',
        label: 'Form',
        dynamic: 'list_forms.id.name',
        required: true,
        list: false,
        altersDynamicFields: false,
      },
    ],
    type: 'hook',
    performUnsubscribe: {
      body: {
        hookUrl: '{{bundle.subscribeData.id}}',
        form_id: '{{bundle.inputData.form_id}}',
      },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      method: 'DELETE',
      removeMissingValuesFrom: { body: false, params: false },
      url: '{{process.env.BASE_URL}}/api/zapier/webhook',
    },
    performSubscribe: {
      body: {
        hookUrl: '{{bundle.targetUrl}}',
        form_id: '{{bundle.inputData.form_id}}',
      },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      method: 'POST',
      removeMissingValuesFrom: { body: false, params: false },
      url: '{{process.env.BASE_URL}}/api/zapier/webhook',
    },
  },
  display: {
    description: 'Triggers when a new submission is created.',
    hidden: false,
    label: 'New Submission',
  },
  key: 'new_submission',
  noun: 'Submission',
};
