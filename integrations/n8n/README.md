# OpnForm n8n Trigger (Community Node)

A custom n8n node that triggers workflows when new form submissions are received in OpnForm.

## What is OpnForm?

OpnForm is an open-source form builder that allows you to create and manage forms, collect submissions, and integrate with various tools. This n8n integration enables you to automate workflows based on form submissions.

**API Documentation**: [https://docs.opnform.com/api-reference/introduction](https://docs.opnform.com/api-reference/introduction)

## What this integration does

This n8n trigger node automatically receives OpnForm submissions as webhook events in your n8n workflows. When a form is submitted in OpnForm, the integration sends the submission data to your n8n workflow, allowing you to:

- Process form submissions automatically
- Integrate with other services and tools
- Transform and route submission data
- Trigger complex multi-step workflows

## API Endpoints Used

This integration uses the following OpnForm API endpoints:

- `GET /open/workspaces` - Lists available workspaces for dropdown selection
- `GET /open/workspaces/{workspaceId}/forms` - Lists forms in a workspace for dropdown selection
- `GET /open/forms/{formId}/integrations` - Checks if an integration already exists
- `POST /open/forms/{formId}/integrations` - Creates a webhook integration subscription
- `DELETE /open/forms/{formId}/integrations/{integrationId}` - Removes the webhook integration

## Install (local development)

```bash
# From this directory
npm install
npm run build
```

Then in your n8n instance, install this package or load it as a local custom node per n8n docs.

## Authentication

### Creating a Personal Access Token

To use this integration, you need a Personal Access Token from OpnForm with the `manage-integrations` ability.

1. Sign in to your OpnForm account at [https://opnform.com](https://opnform.com)
2. Navigate to **Settings → Access Tokens** (`/home?user-settings=access-tokens`)
3. Click **Create new token**
4. Enter a descriptive name (e.g., "n8n Integration")
5. Select the `manage-integrations` ability (and any other abilities you need)
6. Click **Create** and **copy the token immediately** - you won't be able to see it again

For detailed instructions, see the [OpnForm API Keys documentation](https://docs.opnform.com/api-reference/api-keys).

### Required Abilities

| Ability               | Required For                                              |
| --------------------- | --------------------------------------------------------- |
| `manage-integrations` | **Required** - Creating and managing webhook integrations |
| `workspaces-read`     | **Required** - Listing workspaces for dropdown selection  |
| `forms-read`          | **Required** - Listing forms for dropdown selection       |

### Configuring Credentials in n8n

1. In your n8n workflow, add the OpnForm Trigger node
2. Click on **Credentials** and select **Create New**
3. Enter:
   - **Name**: `OpnForm API` (or any descriptive name)
   - **Base URL**: `https://api.opnform.com` (or your self-hosted OpnForm instance URL)
   - **API Key**: Your Personal Access Token
4. Click **Save** and select this credential in the OpnForm Trigger node

## Usage Instructions

### Setting Up the Trigger

1. **Add the OpnForm Trigger node** to your n8n workflow
2. **Select your credentials** (or create new ones as described above)
3. **Choose a Workspace** from the dropdown menu
4. **Choose a Form** from the dropdown menu (forms will load after selecting a workspace)
5. **Activate the workflow** - The node will automatically:
   - Create a webhook integration in OpnForm
   - Configure the webhook URL to receive submissions
   - Start listening for new form submissions

### Workflow Activation

When you activate your n8n workflow:

- The OpnForm Trigger node will create a webhook integration subscription
- OpnForm will send a POST request to your n8n webhook URL whenever the form receives a submission
- The submission data will be available in subsequent workflow nodes

### Workflow Deactivation

When you deactivate your n8n workflow:

- The OpnForm Trigger node will automatically delete the webhook integration
- No further submissions will be sent to your workflow

## Example Workflows

### Example 1: Send Form Submission to Slack

```
OpnForm Trigger → Set Node (format message) → Slack Node (send message)
```

**Workflow Steps:**

1. **OpnForm Trigger** - Receives form submission
2. **Set Node** - Format the submission data into a Slack-friendly message:
   ```json
   {
   	"text": "New form submission received!",
   	"fields": [
   		{
   			"title": "From",
   			"value": "{{ $json.submitter_email }}",
   			"short": true
   		},
   		{
   			"title": "Submission ID",
   			"value": "{{ $json.id }}",
   			"short": true
   		}
   	]
   }
   ```
3. **Slack Node** - Send the formatted message to a Slack channel

### Example 2: Save Submissions to Google Sheets

```
OpnForm Trigger → Transform Data → Google Sheets Node (append row)
```

**Workflow Steps:**

1. **OpnForm Trigger** - Receives form submission
2. **Code Node** - Transform submission data to match your Google Sheets columns:
   ```javascript
   const submission = $input.item.json;
   return {
   	json: {
   		timestamp: submission.created_at,
   		email: submission.data.email || '',
   		name: submission.data.name || '',
   		message: submission.data.message || '',
   	},
   };
   ```
3. **Google Sheets Node** - Append the transformed data as a new row

### Example 3: Conditional Email Notifications

```
OpnForm Trigger → IF Node (check condition) → Email Node (send notification)
```

**Workflow Steps:**

1. **OpnForm Trigger** - Receives form submission
2. **IF Node** - Check if submission meets certain criteria (e.g., `{{ $json.data.urgency }} === 'high'`)
3. **Email Node** - Send an email notification only for high-priority submissions

## How it works

1. **Load Options**: When configuring the node, it fetches Workspaces and Forms via `/open/workspaces` and `/open/workspaces/{workspaceId}/forms` to populate dropdown menus.
2. **On Enable**: The node subscribes by creating a form integration with `integration_id: 'n8n'` and your n8n webhook URL via `POST /open/forms/{formId}/integrations`.
3. **On Disable**: The node automatically deletes the created integration via `DELETE /open/forms/{formId}/integrations/{integrationId}`.
4. **Webhook Receipt**: When a form submission occurs, OpnForm sends the submission data to your n8n webhook URL, and the payload is forwarded as JSON to the workflow.
