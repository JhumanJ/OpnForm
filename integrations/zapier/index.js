const authentication = require('./authentication');
const newSubmissionTrigger = require('./triggers/new_submission.js');
const listWorkspacesTrigger = require('./triggers/list_workspaces.js');
const listFormsTrigger = require('./triggers/list_forms.js');

module.exports = {
  version: require('./package.json').version,
  platformVersion: require('zapier-platform-core').version,
  requestTemplate: {
    headers: {
      Authorization: 'Bearer {{bundle.authData.api_key}}',
      'X-API-KEY': '{{bundle.authData.api_key}}',
    },
    params: { api_key: '{{bundle.authData.api_key}}' },
    body: {},
  },
  authentication: authentication,
  searches: {},
  triggers: {
    [newSubmissionTrigger.key]: newSubmissionTrigger,
    [listWorkspacesTrigger.key]: listWorkspacesTrigger,
    [listFormsTrigger.key]: listFormsTrigger,
  },
};
