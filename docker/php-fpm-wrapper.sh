#!/bin/bash -ex

[ -L /app/storage ] || {
    echo "Backing up initial storage directory"
    rm -rf /etc/initial-storage
    mv /app/storage /etc/initial-storage
}

[ -d /persist/storage ] || {
    echo "Initialising blank storage dir"
    mkdir -p /persist
    cp -a /etc/initial-storage /persist/storage
    chmod 777 -R /persist/storage
}

touch /var/log/opnform.log
chown opnform /var/log/opnform.log

echo "Linking persistent storage into app"
ln -t /app -sf /persist/storage

read_env() {
    set +x
    . /app/.env
    set -x
}
read_env

[ "x$APP_KEY" != "x" ] || {
    artisan key:generate
    read_env
}
[ "x$JWT_SECRET" != "x" ] || {
    artisan jwt:secret -f
    read_env
}

[ "x$FRONT_API_SECRET" != "x" ] || {
	generate-api-secret.sh
    read_env
}

/usr/sbin/php-fpm8.1

tail -f /var/log/opnform.log
