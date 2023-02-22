# syntax=docker/dockerfile:1.3-labs
FROM php:8.1-cli

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN <<EOF
install-php-extensions \
    apcu \
    bcmath \
    bz2 \
    calendar \
    ffi \
    gd \
    gmp \
    imagick \
    intl \
    mysqli \
    pcntl \
    pcov \
    pdo_mysql \
    pdo_pgsql \
    redis \
    soap \
    sockets \
    sodium \
    xsl \
    zip \
    exif
EOF
