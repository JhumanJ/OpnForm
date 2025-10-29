# OpnForm n8n Trigger (Community Node)

Receive OpnForm submissions in n8n using a programmatic trigger with webhook subscription via the public Integrations API.

## Install (local development)

```bash
# From this directory
npm install
npm run build
```

Then in your n8n instance, install this package or load it as a local custom node per n8n docs.

## Credentials

-   Base URL: `https://api.opnform.com` (or your self-hosted URL)
-   API Key: Personal Access Token with `manage-integrations` ability

## How it works

-   Load options fetch Workspaces and Forms via `/open/workspaces` and `/open/workspaces/{workspaceId}/forms`.
-   On enable, the node subscribes by creating a form integration with `integration_id: 'n8n'` and your webhook URL.
-   On disable, the node deletes the created integration.
-   Incoming webhook payload is forwarded as JSON to the flow.

Optional: Provide a Provider URL (link to your n8n workflow) which OpnForm UI can display for quick navigation.
