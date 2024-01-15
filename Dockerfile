ARG PHP_PACKAGES="php8.1 composer php8.1-common php8.1-pgsql php8.1-redis php8.1-mbstring\
        php8.1-simplexml php8.1-bcmath php8.1-gd php8.1-curl php8.1-zip\
        php8.1-imagick php8.1-bz2 php8.1-gmp php8.1-int php8.1-pcov php8.1-soap php8.1-xsl"

FROM node:20-alpine AS javascript-builder
WORKDIR /app

# It's best to add as few files as possible before running the build commands
# as they will be re-run everytime one of those files changes.
#
# It's possible to run npm install with only the package.json and package-lock.json file.

ADD client/package.json client/package-lock.json ./
RUN npm install

ADD client /app/
RUN npm run build

# syntax=docker/dockerfile:1.3-labs
FROM --platform=linux/amd64 ubuntu:23.04 AS php-dependency-installer

ARG PHP_PACKAGES

RUN apt-get update \
    && apt-get install -y $PHP_PACKAGES composer

WORKDIR /app
ADD composer.json composer.lock artisan ./

# NOTE: The project would build more reliably if all php files were added before running
# composer install.  This would though introduce a dependency which would cause every
# dependency to be re-installed each time any php file is edited.  It may be necessary in
# future to remove this 'optimisation' by moving the `RUN composer install` line after all
# the following ADD commands.

# Running artisan requires the full php app to be installed so we need to remove the
# post-autoload command from the composer file if we want to run composer without
# adding a dependency to all the php files.
RUN sed 's_@php artisan package:discover_/bin/true_;' -i composer.json
ADD app/helpers.php /app/app/helpers.php
RUN composer install --ignore-platform-req=php

ADD app /app/app
ADD bootstrap /app/bootstrap
ADD config /app/config
ADD database /app/database
ADD public public
ADD routes routes
ADD tests tests

# Manually run the command we deleted from composer.json earlier
RUN php artisan package:discover --ansi


FROM --platform=linux/amd64 ubuntu:23.04

# supervisord is a process manager which will be responsible for managing the
# various server processes.  These are configured in docker/supervisord.conf

CMD ["/usr/bin/supervisord"]

WORKDIR /app

ARG PHP_PACKAGES

RUN apt-get update \
    && apt-get install -y \
        supervisor nginx sudo postgresql-15 redis\
        $PHP_PACKAGES php8.1-fpm wget\
    && apt-get clean
RUN wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.39.3/install.sh | bash
RUN . /root/.nvm/nvm.sh && nvm install 20

ADD docker/postgres-wrapper.sh docker/php-fpm-wrapper.sh docker/redis-wrapper.sh docker/nuxt-wrapper.sh docker/generate-api-secret.sh /usr/local/bin/
ADD docker/php-fpm.conf /etc/php/8.1/fpm/pool.d/
ADD docker/nginx.conf /etc/nginx/sites-enabled/default
ADD docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD .env.docker .env
ADD client/.env.docker client/.env

ADD . .

COPY --from=javascript-builder /app/.output/ ./nuxt/
ADD client/.env.docker nuxt/.env
RUN cp -r nuxt/public .
COPY --from=php-dependency-installer /app/vendor/ ./vendor/

RUN chmod a+x /usr/local/bin/*.sh /app/artisan \
    && ln -s /app/artisan /usr/local/bin/artisan \
    && useradd opnform \
    && echo "daemon off;" >> /etc/nginx/nginx.conf\
    && echo "daemonize no" >> /etc/redis/redis.conf\
    && echo "appendonly yes" >> /etc/redis/redis.conf\
    && echo "dir /persist/redis/data" >> /etc/redis/redis.conf


EXPOSE 80
