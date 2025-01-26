# OpnForm Docker

This repositoryfolder contains Docker configurations and build scripts for the [OpnForm](https://github.com/JhumanJ/OpnForm.git) project.

## Repository Structure

- `api/`: Contains the Dockerfile and related files for the API service.
- `client/`: Contains the Dockerfile and related files for the client service.
- `web/`: Contains the Dockerfile and related files for the web service.
- `.env.server.sample`: Sample of the env file for the opnform-server contatiner
- `.env.client.sample`: Sample of the env file for the opnform-client contatiner
- `.env.web.sample`: Sample of the env file for the opnform-web contatiner

## Prerequisites

- Docker

## Building the Docker Images
There is a github action workflow setup to build to images.  It requires the following:

### Variables
| Name | Default | Description |
| ---- | ------- | ----------- |
| REGISTRY_NAME | docker.io | The docker registry |
| OPNFORM_VERSION | latest | The tag to use for which version or OpnForm to build |

### Secrets
| Name | Default | Description |
| ---- | ------- | ----------- |
| REGISTRY_USERNAME | github actor | the user name to log into the registry |
| REGISTRY_PASSWORD | <blank> | the password/token used to login to the registry |

## Running the Docker Images locally

To run the containers locally you can use the ```compose.yml``` file
