# Changelog

## 5.0.0 - 2020-10-31

- Upgrade to Laravel 8
- Update dependencies
- Fixed locale cookie fallback [#221](https://github.com/cretueusebiu/laravel-vue-spa/pull/221)
- Added scroll delay [#220](https://github.com/cretueusebiu/laravel-vue-spa/pull/220)
- Added redirect to intended url [#208](https://github.com/cretueusebiu/laravel-vue-spa/pull/208)
- Added middleware parameter [#124](https://github.com/cretueusebiu/laravel-vue-spa/pull/124)

## 4.7.0 - 2020-04-09

- Added `routes/spa.php` and `spa` middleware for serving the frontend [#249](https://github.com/cretueusebiu/laravel-vue-spa/pull/249)
- Added controllers for user and spa view to fix route caching [#248](https://github.com/cretueusebiu/laravel-vue-spa/pull/248)

## 4.6.0 - 2020-03-25

- Upgrade to Laravel 7

## 4.5.0 - 2019-09-29

- Upgrade to Laravel 6

## 4.4.0 - 2019-05-17

- Upgrade to Laravel 5.8
- Update npm dependencies
- Added email verification
- Removed yarn.lock in favor of package-lock.json
- Fixed `laravel-mix` (HMR, versioning, build cleanup), thanks to [TemaSM](https://github.com/TemaSM)
- Removed JS polyfills, if you want to support IE11 see [laravel-mix#436](https://github.com/JeffreyWay/laravel-mix/issues/436) 

## 4.3.0 - 2018-10-21

- Fixed npm install
- Update fontawesome

## 4.2.0 - 2018-10-04

- Upgrade to Laravel 5.7

## 4.1.1 - 2018-05-10

- Fixed oauth popup
- Fixed session expired alert
- Update npm dependencies

## 4.1.0 - 2018-03-14

- Upgrade to Laravel 5.6
- Update npm dependencies
- Enabled Mix [esModule](https://github.com/JeffreyWay/laravel-mix/pull/1526#issuecomment-373044182) by default
- Lint with eslint-plugin-vue and eslint-config-standard 

## 4.0.0 - 2018-01-23

- Brought back the `layout` property in pages that was removed in 3.0. By default it's set to `default` (`~/layouts/default.vue`).

## 3.0.1 - 2018-01-22

- Removed middleware from routes, since they don't work when you press the back button. Now you have to use the `middleware` property in pages.

## 3.0.0 - 2018-01-22

- Removed `layout` property from pages in favor of explicit layout components
- Improved middleware system, you can either define it on a route or directly on a component
- Reorganized components
- Change jwt token ttl to one day
- Fixed  hot module replacement

## 2.2.1 - 2018-01-19

- Upgrade to Bootstrap 4

## 2.2.0 - 2018-01-16

- Added dynamic import for pages
- Added locale with dynamic import
- Import shakable fontawesome module

## 2.1.0 - 2018-01-10

- Simplifyd password reset
- Upgrade to Bootstrap 4 beta3
- Added oauth with popup window

## 2.0.0 - 2017-12-19

- Added support for Socialite
- Added dropdown to select the locale 
- Added route middleware 
- Added Font Awesome 5 icons
- Added user profile photo in navbar
- Added Chinese and Spanish translations
- Upgrade to Bootstrap 4 beta2
- Fixed router scroll behaviour
- Namespaced store modules  
- Published project to Packagist 

## 1.0.0 - 2017-09-02

- Upgrade to Laravel 5.5.
- Upgrade to Bootstrap 4 beta1.
- Rework routing and guards.
