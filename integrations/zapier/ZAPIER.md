# Zapier

Install Zapier

```
npm install -g zapier-platform-cli
```

Install dependencies

```
cd `zapier`
npm install
```

Login to Zapier

```
zapier login
```

Register the app

```
zapier register [TITLE]
```

Publish the app

```
zapier push
```

Set the base URL to receive webhooks from Zapier. The version usually looks like 1.0.0.

```
zapier env:set [VERSION] BASE_URL=[BASE_URL]
```

## Testing

-   Create an access token: http://localhost:3000/settings/access-tokens
-   Create a Zap
-   Authenticate using your token
-   Submit a form
