# OpnForm

<p align="center">
<img src="https://github.com/JhumanJ/OpnForm/blob/main/public/img/social-preview.jpg?raw=true">
</p>

<a href="https://github.com/jhumanj/OpnForm/actions"><img src="https://github.com/jhumanj/laravel-vue-tailwind-spa/workflows/tests/badge.svg" alt="Build Status"></a>

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

## Getting started with OpnForm

The easiest way to get started with OpnForm is with the [official managed service in the Cloud](https://opnform.com/).

It takes 1 minute to try out the builder for free. You'll have high availability, backups, security, and maintenance all managed for you.

### Requirements

- PHP >= 8.0
- MySQL/MariaDB or PostgreSQL
- Node.js and NPM/Yarn/... to compile assets

## Installation


### Docker installation 🐳

There's a `Dockerfile` for building a self-contained docker image including databases, webservers etc.

This can be built and run locally but is also hosted publicly on docker hub at `jhumanj/opnform` and is generally best run directly from there.

#### Running from docker hub

```
docker run -v my-opnform-data:/persist -p 80:80 jhumanj/opnform
```

You should now be able to access the application by visiting  http://localhost in a web browser.

The `-v` argument creates a local directory called `my-opnform-data` which will store your database and files so that your work is not lost when you restart the container.


#### Using a custom .env file

If you have a custom env file you can use this like so:

```
docker run -v my-custom-env-file.env:/app/.env -v my-opnform-data:/persist -p 80:80 jhumanj/opnform
```

This would load load in the env file located at `my-custom-env-file.env`, note that if you are creating a .env file for use like this it's best to start from the `.docker.env` example file as there are slightly different defaults for the dockerized setup.

#### Using a custom HTTP port

To run on port 8080

```
docker run -v my-opnform-data:/persist -p 8080:80 jhumanj/opnform
```

#### Building a custom docker image

To build a custom docker image from your local source code use this command from the root of the source repository:

```
docker build . -t my-docker-image-name
```

This should create a new docker image tagged `my-docker-image-name` which can be run as follows:

```
docker run -v my-opnform-data:/persist -p 80:80 my-docker-image-name

```

### Using Laravel Valet
This section explains how to get started locally with the project. It's most likely relevant if you're trying to work on the project.
First, let's work with the codebase and its dependencies.

```bash
# Get the code!
git clone git@github.com:JhumanJ/OpnForm.git && cd OpnForm

# Install PHP and JS dependencies
composer install && npm install

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

## Tech Stack

OpnForm is a standard web application built with:
- [Laravel](https://laravel.com/) PHP framework
- [Vue.js](https://vuejs.org/) front-end framework
- [TailwindCSS](https://tailwindcss.com/)

## Contribute
You're more than welcome to contribute to this project. We don't have guidelines on this yet, but we will soon. In the meantime, feel free to ask [any question here](https://github.com/JhumanJ/OpnForm/discussions).

## License
OpnForm is open-source under the GNU Affero General Public License Version 3 (AGPLv3) or any later version. You can find it [here](https://github.com/JhumanJ/OpnForm/blob/main/LICENSE).

