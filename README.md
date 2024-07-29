# OpnForm

<p align="center">
<img src="https://github.com/JhumanJ/OpnForm/blob/main/public/img/social-preview.jpg?raw=true">
</p>


<p align="center">
<a href="https://github.com/JhumanJ/OpnForm/stargazers"><img src="https://img.shields.io/github/stars/JhumanJ/OpnForm" alt="Github Stars"></a>
</a>
<a href="https://github.com/JhumanJ/OpnForm/pulse"><img src="https://img.shields.io/github/commit-activity/m/JhumanJ/OpnForm" alt="Commits per month"></a>
<a href="https://hub.docker.com/r/jhumanj/opnform-api">
<img src="https://img.shields.io/docker/pulls/jhumanj/opnform-api">
</a>
<a href="https://github.com/JhumanJ/OpnForm/blob/main/LICENSE"><img src="https://img.shields.io/badge/license-AGPLv3-purple" alt="License">
<a href="https://github.com/JhumanJ/OpnForm/issues/new"><img src="https://img.shields.io/badge/Report a bug-Github-%231F80C0" alt="Report a bug"></a>
<a href="https://github.com/JhumanJ/OpnForm/discussions/new?category=q-a"><img src="https://img.shields.io/badge/Ask a question-Github-%231F80C0" alt="Ask a question"></a>
<a href="https://feedback.opnform.com"><img src="https://img.shields.io/badge/Feature request-Featurebase-%231F80C0" alt="Ask a question"></a>
<a href="https://discord.gg/YTSjU2a9TS"><img src="https://dcbadge.vercel.app/api/server/YTSjU2a9TS?style=flat" alt="Ask a question"></a>
<a href="https://console.algora.io/org/OpnForm/bounties?status=open"><img src="https://img.shields.io/endpoint?url=https%3A%2F%2Fconsole.algora.io%2Fapi%2Fshields%2FOpnForm%2Fbounties%3Fstatus%3Dopen" alt="Open Bounties"></a>
<a href="https://console.algora.io/org/OpnForm/bounties?status=completed"><img src="https://img.shields.io/endpoint?url=https%3A%2F%2Fconsole.algora.io%2Fapi%2Fshields%2FOpnForm%2Fbounties%3Fstatus%3Dcompleted" alt="Rewarded Bounties"></a>
</p>

> An open-source form builder. It's an alternative to products like Typeform, JotForm, Tally etc.

## Features

- No-code form builder, with infinite number of fields & submissions
- Text inputs, Date inputs, URL inputs, Phone inputs, Email inputs, Checkboxes, Select and Multi-Select inputs, Number Inputs, Star-ratings, File uploads & more 
- Embed anywhere (on your website, in your Notion page, etc)
- Email notifications (for both form owner & form respondents)
- Hidden fields
- Form passwords
- URL form pre-fill
- Slack integration
- Webhooks
- Form logic
- Customize colors, add images or even some custom code
- Captcha form protection
- Form closing date
- Limit the number of submissions allowed

And much more!

## Bounties
Get paid for contributing to OpnForm! Here are our open bounties:

<a href="https://console.algora.io/org/OpnForm/bounties?status=open">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://console.algora.io/api/og/OpnForm/bounties.png?p=0&status=open&theme=dark">
    <img alt="Bounties of OpnForm" src="https://console.algora.io/api/og/OpnForm/bounties.png?p=0&status=open&theme=light">
  </picture>
</a>

## Getting started with OpnForm

The easiest way to get started with OpnForm is with the [official managed service in the Cloud](https://opnform.com/).

It takes 1 minute to try out the builder for free. You'll have high availability, backups, security, and maintenance all managed for you.

### Requirements

- PHP >= 8.0
- MySQL/MariaDB or PostgreSQL
- Node.js and NPM/Yarn/... to compile assets

## Installation


### Docker installation 🐳

You can build the images locally but they are also available pre-built on dockerhub and it's generally best to run them from there.  A docker-compose.yml file is provided which should set up all the necessary services to get running.

#### Requirements
- docker
- docker-compose

#### Running from docker hub

```
cp .env.docker .env
cp client/.env.docker client/.env
docker-compose up
```

You should now be able to access the application by visiting  http://localhost in a web browser. 

> 👀 **Server Deployment**: If you are deploying OpnForm on a server (not locally), then you will [need to use 2 .env files](https://github.com/JhumanJ/opnform?tab=readme-ov-file#using-custom-env-files) to configure the app URLs (`APP_URL` in `.env` and both `NUXT_PUBLIC_APP_URL` & `NUXT_PUBLIC_API_BASE` in `client/.env`).


#### Using custom .env files

The docker-compose set up will load in the .env and client/.env files.

*** FIXME If you run the system without providing .env files then it will never be possible to provide them ***


#### Upgrading docker installations

**Please consult the upgrade instructions for the latest opnform version**, e.g. if upgrading from v1 to v2 please check the v2 instructions as the process may change in future releases.

Normal upgrade procedure would be to update the docker-compose.yml file(s) and then apply the changes by re-running:
```
docker-compose up 
```

### Using Laravel Valet
This section explains how to get started locally with the project. It's most likely relevant if you're trying to work on the project.
First, let's work with the codebase and its dependencies.

```bash
# Get the code!
git clone git@github.com:JhumanJ/OpnForm.git && cd OpnForm

# Install PHP dependencies
composer install 
 
 # Install JS dependencies
cd client && npm install

# Compile assets (see the scripts section in package.json)
npm run dev # or build
```

Now, we can configure Laravel. We just need to prepare some vars in our `.env` file, just create it with `cp .env.example .env` then open it!

Configure the desired database in the `DATABASE_` section. You can fine tune your installation on the [laravel documentation](https://laravel.com/docs/9.x).

Run these artisan commands:

```bash
# Generate needed secrets 🙈
php artisan key:generate
php artisan jwt:secret # and select yes!

# Creates DB schemas
php artisan migrate
```
Now, create an S3 bucket (or equivalent). Create an IAM user with access to this bucket, fill the environment variables: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`. In your AWS bucket permissions, add the following under "Cross-origin resource sharing (CORS)": 
```json
[ { "AllowedHeaders": [ "*" ], "AllowedMethods": [ "PUT", "POST", "GET", "DELETE" ], "AllowedOrigins": [ "*" ], "ExposeHeaders": [] } ]
```

🎉 Done! Enjoy your personal OpnForm instance at: [http://opnform.test](http://opnform.test).

## One-Click Deployment

[![Deploy to RepoCloud](https://d16t0pc4846x52.cloudfront.net/deploylobe.svg)](https://repocloud.io/details/?app_id=294)

## Tech Stack

OpnForm is a standard web application built with:
- [Laravel](https://laravel.com/) PHP framework
- [NuxtJs](https://nuxt.com/) Front-end SSR framework
- [Vue.js 3](https://vuejs.org/) Front-end framework
- [TailwindCSS](https://tailwindcss.com/)

## Contribute
You're more than welcome to contribute to this project. We don't have guidelines on this yet, but we will soon. In the meantime, feel free to ask [any question here](https://github.com/JhumanJ/OpnForm/discussions).

## License
OpnForm is open-source under the GNU Affero General Public License Version 3 (AGPLv3) or any later version. You can find it [here](https://github.com/JhumanJ/OpnForm/blob/main/LICENSE).

