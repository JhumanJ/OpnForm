module.exports = {
  type: 'custom',
  test: {
    removeMissingValuesFrom: { body: false, params: false },
    url: '{{process.env.BASE_URL}}/api/zapier/validate',
  },
  fields: [
    {
      computed: false,
      key: 'api_key',
      required: true,
      label: 'API Key',
      type: 'string',
    },
  ],
  connectionLabel: '{{bundle.inputData.name}} {{bundle.inputData.email}}',
  customConfig: {},
};
