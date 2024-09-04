---
name: Deployment/Self-hosted Issue
about: Report an issue related to deployment or self-hosting
title: "[DEPLOYMENT] "
labels: deployment
assignees: ""
---

**Describe the issue**
A clear and concise description of the deployment or self-hosting issue you're experiencing.

**Deployment Environment**

-   OpnForm Version: [e.g. 1.0.22]
-   Hosting Platform: [e.g. AWS, DigitalOcean, Self-hosted server]
-   OS: [e.g. Ubuntu 20.04, CentOS 8]

**Deployment Method**

-   [ ] Docker
-   [ ] Manual installation
-   [ ] Other (please specify)

**Steps Taken**
Describe the steps you've taken to deploy the application:

1.
2.
3.

**Error Messages**
If applicable, provide any error messages or logs related to the issue. If it's only a generic error message (e.g. "Server Error") please set `APP_DEBUG=true` in `api/.env`, try again and provide use the error message.

**Configuration Files**
If relevant, provide snippets of your configuration files (make sure to remove any sensitive information).

**Logs**
To help us diagnose the issue, please provide the following logs:

-   `laravel.log` in `api/storage/logs` on the back-end image
-   Nuxt logs in the client docker logs

**Additional context**
Add any other context about the deployment issue here.
