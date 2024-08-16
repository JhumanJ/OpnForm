module.exports = {
  type: 'custom',
  test: {
    removeMissingValuesFrom: { body: false, params: false },
    url: '{{process.env.BASE_URL}}/external/zapier/validate',
  },
  fields: [
    {
      helpText:
        'Enter your API key, located at https://opnform.com/settings/access-tokens',
      computed: false,
      key: 'api_key',
      required: true,
      label: 'API Key',
      type: 'string',
    },
  ],
  connectionLabel: '{{bundle.inputData.email}}',
  customConfig: {},
};
