# Deploy OpnForm with Coolify

This document provides instructions for deploying OpnForm using Coolify, enabling users to set up the application with a single click.

## 1-Click Deploy OpnForm

To deploy OpnForm with Coolify, click the button below:

[![Deploy to Coolify](https://img.shields.io/badge/Deploy%20to-Coolify-brightgreen)](https://coolify.io/deploy?template=https://github.com/aybanda/coolify-template/blob/main/template.yml)

### Additional Information

For more details on configuring your deployment, refer to the [Coolify documentation](https://coolify.io/docs).

## Template Details

This Coolify template includes the following services necessary for running OpnForm:

-   **opnform-api**: The main API service for OpnForm.
-   **db**: PostgreSQL database service.
-   **redis**: Redis service for caching.
-   **opnform-ingress**: Nginx ingress service for routing HTTP requests.

### Environment Variables

The following environment variables are utilized in the template to configure the services:

-   `DB_HOST`: Hostname for the database service.
-   `REDIS_HOST`: Hostname for the Redis service.
-   `DB_DATABASE`: Name of the database (default: `forge`).
-   `DB_USERNAME`: Database username (default: `forge`).
-   `DB_PASSWORD`: Database password (default: `forge`).
-   `DB_CONNECTION`: Database connection type (default: `pgsql`).

### How to Contribute

If you would like to contribute to this template or report issues, please visit the [GitHub repository](https://github.com/aybanda/coolify-template). Your contributions help improve the deployment experience for all OpnForm users.

### Related Issue

This template addresses the request for a Coolify deployment option for OpnForm as outlined in [Issue #626](https://github.com/JhumanJ/OpnForm/issues/626).
