# Laravel-Vue-Tailwind SPA 

<a href="https://github.com/jhumanj/laravel-vue-tailwind-spa/actions"><img src="https://github.com/jhumanj/laravel-vue-tailwind-spa/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/jhumanj/laravel-vue-tailwind-spa"><img src="https://poser.pugx.org/jhumanj/laravel-vue-tailwind-spa/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jhumanj/laravel-vue-tailwind-spa"><img src="https://poser.pugx.org/jhumanj/laravel-vue-tailwind-spa/v/stable.svg" alt="Latest Stable Version"></a>

> A Laravel-Vue-Tailwind SPA starter kit. Forked from [cretueusebiu/laravel-vue-spa](https://github.com/cretueusebiu/laravel-vue-spa).

<p align="center">
<img src="https://i.ibb.co/XCcPJXR/Capture-d-e-cran-2021-04-14-a-17-52-07.png">
</p>

## Features

- Laravel 8
- Vue + VueRouter + Vuex + VueI18n + ESlint
- Pages with dynamic import and custom layouts
- Login, register, email verification and password reset
- Authentication with JWT
- Socialite integration
- TailwindCSS v2

## Installation

- `composer create-project --prefer-dist JhumanJ/laravel-vue-tailwind-spa`
- Edit `.env` and set your database connection details
- (When installed via git clone or download, run `php artisan key:generate` and `php artisan jwt:secret`)
- `php artisan migrate`
- `npm install`

## Usage

#### Development

```bash
# Build and watch
npm run watch
```

#### Production

```bash
npm run production
```

## Socialite

This project comes with GitHub as an example for [Laravel Socialite](https://laravel.com/docs/5.8/socialite).

To enable the provider create a new GitHub application and use `https://example.com/api/oauth/github/callback` as the Authorization callback URL.

Edit `.env` and set `GITHUB_CLIENT_ID` and `GITHUB_CLIENT_SECRET` with the keys form your GitHub application.

For other providers you may need to set the appropriate keys in `config/services.php` and redirect url in `OAuthController.php`.

## Email Verification

To enable email verification make sure that your `App\User` model implements the `Illuminate\Contracts\Auth\MustVerifyEmail` contract.

## Testing

```bash
# Run unit and feature tests
vendor/bin/phpunit

# Run Dusk browser tests
php artisan dusk
```

## Credits
- [cretueusebiu](https://github.com/cretueusebiu/) for creating the original [Laravel Vue SPA](https://github.com/cretueusebiu/laravel-vue-spa)
- [Tailwind Kit](https://www.tailwind-kit.com/) for all their Tailwind templates

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.
