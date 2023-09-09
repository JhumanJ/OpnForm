ARG PHP_PACKAGES="php8.1 composer php8.1-common php8.1-pgsql php8.1-redis php8.1-mbstring\
        php8.1-simplexml php8.1-bcmath php8.1-gd php8.1-curl php8.1-zip\
        php8.1-imagick php8.1-bz2 php8.1-gmp php8.1-int php8.1-pcov php8.1-soap php8.1-xsl"

FROM node:16 AS javascript-builder
WORKDIR /app

# It's best to add as few files as possible before running the build commands
# as they will be re-run everytime one of those files changes.
#
# It's possible to run npm install with only the package.json and package-lock.json file.

ADD package.json package-lock.json ./
RUN npm install

ADD resources /app/resources
ADD public /app/public
ADD tailwind.config.js vite.config.js postcss.config.js /app/
RUN npm run build


# syntax=docker/dockerfile:1.3-labs
FROM --platform=linux/amd64 ubuntu:23.04 AS php-dependency-installer

ARG PHP_PACKAGES

RUN apt-get update \
    && apt-get install -y $PHP_PACKAGES composer

WORKDIR /app
ADD composer.json composer.lock artisan ./
# Running artisan requires the full php app to be installed so we need to remove the
# post-autoload command from the composer file if we want to run composer without
# adding a dependency to all the php files.
RUN sed 's_@php artisan package:discover_/bin/true_;' -i composer.json
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
        $PHP_PACKAGES php8.1-fpm\
    && apt-get clean

ADD docker/postgres-wrapper.sh docker/php-fpm-wrapper.sh docker/redis-wrapper.sh /usr/local/bin/
ADD docker/php-fpm.conf /etc/php/8.1/fpm/pool.d/
ADD docker/nginx.conf /etc/nginx/sites-enabled/default
ADD docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD .env.docker .env

ADD . .

COPY --from=javascript-builder /app/public/build/ ./public/build/
COPY --from=php-dependency-installer /app/vendor/ ./vendor/

RUN chmod a+x /usr/local/bin/*.sh /app/artisan \
    && ln -s /app/artisan /usr/local/bin/artisan \
    && useradd opnform \
    && echo "daemon off;" >> /etc/nginx/nginx.conf\
    && echo "daemonize no" >> /etc/redis/redis.conf\
    && echo "appendonly yes" >> /etc/redis/redis.conf\
    && echo "dir /persist/redis/data" >> /etc/redis/redis.conf


EXPOSE 80
