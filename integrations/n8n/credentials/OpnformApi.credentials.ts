import type {
	IAuthenticateGeneric,
	Icon,
	ICredentialTestRequest,
	ICredentialType,
	INodeProperties,
} from 'n8n-workflow';

export class OpnformApi implements ICredentialType {
	name = 'opnformApi';
	displayName = 'OpnForm API';
	icon: Icon = 'file:../icons/opnform.svg';
	documentationUrl = 'https://docs.opnform.com';
	properties: INodeProperties[] = [
		{
			displayName: 'API Key',
			name: 'apiKey',
			type: 'string',
			typeOptions: { password: true },
			default: '',
			description: 'Personal Access Token with manage-integrations ability. Create one at https://opnform.com/home?user-settings=access-tokens',
		},
		{
			displayName: 'Base URL',
			name: 'baseUrl',
			type: 'string',
			default: 'https://api.opnform.com',
		},
		{
			displayName: 'n8n Instance URL',
			name: 'n8nInstanceUrl',
			type: 'string',
			default: '',
			required: false,
			description: 'Optional. URL of your n8n instance (auto-detected from webhook URL). Override if needed for self-hosted instances.',
			placeholder: 'https://n8n.example.com',
		},
	];

	authenticate: IAuthenticateGeneric = {
		type: 'generic',
		properties: {
			headers: {
				Authorization: '=Bearer {{$credentials.apiKey}}',
			},
		},
	};

	test: ICredentialTestRequest = {
		request: {
			url: '={{$credentials.baseUrl.replace(/\\/$/, "").trim()}}/open/workspaces',
		},
	};
}


