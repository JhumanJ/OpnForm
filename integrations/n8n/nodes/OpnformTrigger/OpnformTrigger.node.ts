import type {
	IHookFunctions,
	ILoadOptionsFunctions,
	IWebhookFunctions,
	INodeExecutionData,
	INodeType,
	INodeTypeDescription,
	IDataObject,
} from 'n8n-workflow';

type WorkspaceSummary = { id: number; name: string };
type FormSummary = { id: number | string; name?: string; title?: string };

export class OpnformTrigger implements INodeType {
	description: INodeTypeDescription = {
		displayName: 'OpnForm Trigger',
		name: 'opnformTrigger',
		icon: 'file:../../icons/opnform.svg',
		group: ['trigger'],
		version: 1,
		description: 'Triggers when a new submission is created in OpnForm',
		documentationUrl: 'https://docs.opnform.com',
		defaults: { name: 'OpnForm Trigger' },
		inputs: [],
		outputs: ['main'],
		credentials: [
			{ name: 'opnformApi', required: true },
		],
		properties: [
			{
				displayName: 'Workspace Name or ID',
				name: 'workspaceId',
				type: 'options',
				typeOptions: {
					loadOptionsMethod: 'getWorkspaces',
				},
				default: '',
				required: true,
				description: 'Choose from the list, or specify an ID using an <a href="https://docs.n8n.io/code/expressions/">expression</a>',
			},
			{
				displayName: 'Form Name or ID',
				name: 'formId',
				type: 'options',
				typeOptions: {
					loadOptionsMethod: 'getForms',
				},
				default: '',
				required: true,
				description: 'Choose from the list, or specify an ID using an <a href="https://docs.n8n.io/code/expressions/">expression</a>',
				displayOptions: {
					hide: {
						workspaceId: [''],
					},
				},
			},
		],
		usableAsTool: true,
	};

	methods = {
		loadOptions: {
			async getWorkspaces(this: ILoadOptionsFunctions) {
				const credentials = await this.getCredentials('opnformApi') as { baseUrl: string };
				const url = `${credentials.baseUrl}/open/workspaces`;
				const ws = await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', { url, json: true });
				return (ws as WorkspaceSummary[]).map(w => ({ name: w.name, value: String(w.id) }));
			},
			async getForms(this: ILoadOptionsFunctions) {
				const credentials = await this.getCredentials('opnformApi') as { baseUrl: string };
				const workspaceId = this.getNodeParameter('workspaceId', 0) as string;
				if (!workspaceId) {
					return [];
				}
				const url = `${credentials.baseUrl}/open/workspaces/${workspaceId}/forms`;
				const response = await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', { 
					url, 
					qs: { per_page: 100 },
					json: true 
				});
				// Handle paginated response - extract data array
				const forms = (response?.data ?? response) as FormSummary[];
				if (!Array.isArray(forms)) {
					return [];
				}
				return forms.map(f => ({ name: f.name ?? f.title ?? String(f.id), value: String(f.id) }));
			},
		},
		webhook: {
			async checkExists(this: IHookFunctions) {
				return false;
			},
			async create(this: IHookFunctions) {
				const credentials = await this.getCredentials('opnformApi') as { baseUrl: string };
				const baseUrl = credentials.baseUrl;
				const formId = this.getNodeParameter('formId') as string;
				const hookUrl = this.getNodeWebhookUrl('default');

				// Auto-generate provider URL pointing to n8n workflow
				const providerUrl = this.getNode().type === 'n8n-nodes-opnform.opnformTrigger'
					? `${this.getExecutionId()}`
					: undefined;

				const body = {
					integration_id: 'n8n',
					status: true,
					data: {
						webhook_url: hookUrl,
						...(providerUrl ? { provider_url: providerUrl } : {}),
					},
				};

				const response = await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', {
					method: 'POST',
					url: `${baseUrl}/open/forms/${formId}/integrations`,
					body,
					json: true,
				});

				const integrationId = (response?.form_integration?.id ?? response?.id) as number | undefined;
				if (integrationId) {
					const staticData = this.getWorkflowStaticData('node');
					staticData.integrationId = String(integrationId);
				}
				return true;
			},
			async delete(this: IHookFunctions) {
				const credentials = await this.getCredentials('opnformApi') as { baseUrl: string };
				const baseUrl = credentials.baseUrl;
				const formId = this.getNodeParameter('formId') as string;
				const staticData = this.getWorkflowStaticData('node') as { integrationId?: string };
				if (!staticData.integrationId) {
					return true;
				}
				await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', {
					method: 'DELETE',
					url: `${baseUrl}/open/forms/${formId}/integrations/${staticData.integrationId}`,
					json: true,
				});
				delete staticData.integrationId;
				return true;
			},
		},
	};

	async webhook(this: IWebhookFunctions) {
		const body = this.getBodyData();
		const json: IDataObject = (typeof body === 'object' && body !== null)
			? (body as unknown as IDataObject)
			: { value: body as unknown } as IDataObject;
		const items: INodeExecutionData[] = [{ json }];
		return { workflowData: [items] };
	}
}


