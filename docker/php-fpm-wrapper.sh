#!/bin/bash +ex

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
ln -sf /persist/storage /app/storage

. /app/.env

[ "x$APP_KEY" != "x" ] || {
    artisan key:generate
    . /app/.env
}
[ "x$JWT_SECRET" != "x" ] || {
    artisan jwt:secret -f
    . /app/.env
}

[ "x$FRONT_API_SECRET" != "x" ] || {
	generate-api-secret.sh
    . /app/.env
}

/usr/sbin/php-fpm8.1

tail -f /var/log/opnform.log
