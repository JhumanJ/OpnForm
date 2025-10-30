import type {
	IHookFunctions,
	ILoadOptionsFunctions,
	IWebhookFunctions,
	INodeExecutionData,
	INodeType,
	INodeTypeDescription,
	IDataObject,
} from 'n8n-workflow';
import { NodeOperationError } from 'n8n-workflow';

type WorkspaceSummary = { id: number; name: string };
type FormSummary = { id: number | string; name?: string; title?: string };
type Integration = {
	id: number;
	form_id: number;
	integration_id: string;
	status: string;
	data: {
		webhook_url?: string;
		provider_url?: string;
	};
};

/**
 * Normalize base URL by removing trailing slashes and whitespace
 */
function normalizeBaseUrl(url: string): string {
	return url.replace(/\/+$/, '').trim();
}

/**
 * Get normalized credentials with baseUrl already cleaned
 */
async function getNormalizedCredentials(context: ILoadOptionsFunctions | IHookFunctions): Promise<{ baseUrl: string; apiKey: string }> {
	const credentials = await context.getCredentials('opnformApi') as { baseUrl: string; apiKey: string };
	return {
		...credentials,
		baseUrl: normalizeBaseUrl(credentials.baseUrl),
	};
}

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
		webhooks: [
			{
				name: 'default',
				httpMethod: 'POST',
				responseMode: 'onReceived',
				path: 'webhook',
			},
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
					loadOptionsDependsOn: ['workspaceId'],
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
				const credentials = await getNormalizedCredentials(this);
				const url = `${credentials.baseUrl}/open/workspaces`;
				const ws = await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', { url, json: true });
				return (ws as WorkspaceSummary[]).map(w => ({ name: w.name, value: String(w.id) }));
			},
			async getForms(this: ILoadOptionsFunctions) {
				const workspaceId = this.getNodeParameter('workspaceId', 0) as string;
				if (!workspaceId) {
					return [];
				}
				const credentials = await getNormalizedCredentials(this);
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
	};

	webhookMethods = {
		default: {
			async checkExists(this: IHookFunctions) {
				const credentials = await getNormalizedCredentials(this);
				const formId = this.getNodeParameter('formId') as string;
				const staticData = this.getWorkflowStaticData('node') as { integrationId?: string };
				
				// If we don't have a stored integration ID, it doesn't exist
				if (!staticData.integrationId) {
					return false;
				}
				
				try {
					// Fetch all integrations for this form
					const url = `${credentials.baseUrl}/open/forms/${formId}/integrations`;
					const integrations = await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', {
						url,
						json: true,
					}) as Integration[];
					
					if (!Array.isArray(integrations)) {
						return false;
					}
					
					// Check if our stored integration ID exists and matches n8n integration
					const storedId = String(staticData.integrationId);
					const exists = integrations.some(
						(integration: Integration) =>
							String(integration.id) === storedId &&
							integration.integration_id === 'n8n'
					);
					
					return exists;
				} catch {
					// If API call fails, assume it doesn't exist
					return false;
				}
			},
			async create(this: IHookFunctions) {
				const credentials = await getNormalizedCredentials(this);
				const formId = this.getNodeParameter('formId') as string;
				const hookUrl = this.getNodeWebhookUrl('default');

				if (!formId) {
					throw new NodeOperationError(this.getNode(), 'Form ID is required');
				}

				if (!hookUrl) {
					throw new NodeOperationError(this.getNode(), 'Webhook URL is required');
				}

				const staticData = this.getWorkflowStaticData('node') as { 
					integrationId?: string;
					lastFormId?: string;
				};

				// If formId changed, delete the old integration first
				if (staticData.lastFormId && staticData.lastFormId !== formId && staticData.integrationId) {
					try {
						await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', {
							method: 'DELETE',
							url: `${credentials.baseUrl}/open/forms/${staticData.lastFormId}/integrations/${staticData.integrationId}`,
							json: true,
						});
					} catch {
						// Continue anyway, don't fail the new creation
					}
				}

				// Construct provider URL if n8n instance URL is configured
				let providerUrl: string | undefined;
				try {
					let n8nInstanceUrl = (credentials as { baseUrl: string; apiKey: string; n8nInstanceUrl?: string }).n8nInstanceUrl;
					
					// If not explicitly configured, extract from webhook URL
					// Webhook URL format: https://n8n-instance.com/webhook/workflow-id/trigger-name
					if (!n8nInstanceUrl && hookUrl) {
						const webhookUrl = new URL(hookUrl);
						n8nInstanceUrl = `${webhookUrl.protocol}//${webhookUrl.host}`;
					}
					
					if (n8nInstanceUrl) {
						const workflowId = this.getWorkflow()?.id;
						if (workflowId) {
							providerUrl = `${n8nInstanceUrl.replace(/\/$/, '')}/workflow/${workflowId}`;
						}
					}
				} catch {
					// Silently fail, provider URL is optional
				}

				const body = {
					integration_id: 'n8n',
					status: true,
					settings: {
						webhook_url: hookUrl,
						...(providerUrl && { provider_url: providerUrl }),
					},
				};

				const response = await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', {
					method: 'POST',
					url: `${credentials.baseUrl}/open/forms/${formId}/integrations`,
					body,
					json: true,
				});

				const integrationId = (response?.form_integration?.id ?? response?.id) as number | undefined;
				
				if (!integrationId) {
					throw new NodeOperationError(this.getNode(), `Failed to create integration. Response: ${JSON.stringify(response)}`);
				}

				staticData.integrationId = String(integrationId);
				staticData.lastFormId = formId;
				return true;
			},
			async delete(this: IHookFunctions) {
				const credentials = await getNormalizedCredentials(this);
				const formId = this.getNodeParameter('formId') as string;
				const staticData = this.getWorkflowStaticData('node') as { integrationId?: string };
				
				if (!staticData.integrationId) {
					return true;
				}
				
				const url = `${credentials.baseUrl}/open/forms/${formId}/integrations/${staticData.integrationId}`;
				
				await this.helpers.httpRequestWithAuthentication.call(this, 'opnformApi', {
					method: 'DELETE',
					url,
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


