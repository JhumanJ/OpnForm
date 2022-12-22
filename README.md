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

## Self-hosting

🚧 This section is under construction!

### Requirements

- PHP >= 8.0
- MySQL/MariaDB or PostgreSQL
- Node.js and NPM/Yarn/... to compile assets

### Local installation

First, let's work with the codebase and its dependencies.

```bash
# Get the code!
git clone git@github.com:JhumanJ/OpnForm.git && cd OpnForm

# Install PHP and JS dependencies
composer install && npm install

# Compile assets (see the scripts section in package.json)
npm run dev # or prod, or watch
```

Now, we can configure Laravel. We just need to prepare some vars in our `.env` file, just create it with `cp .env.example .env` then open it!

Configure the desired database in the `DATABASE_` section. You can fine tune your installation on the [laravel documentation](https://laravel.com/docs/9.x).

Finally, just run these artisan commands and you're done!

```bash
# Generate needed secrets 🙈
php artisan key:generate
php artisan jwt:secret # and select yes!

# Creates DB schemas
php artisan migrate
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

