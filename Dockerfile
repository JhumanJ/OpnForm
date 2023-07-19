FROM --platform=linux/amd64 ubuntu:23.04


CMD ["/usr/bin/supervisord"]

WORKDIR /app
ADD . .

RUN ln -s /app/artisan /usr/local/bin/artisan \
    && apt-get update \
    && apt-get install -y \
        supervisor nginx sudo postgresql-15 redis\
        php8.1 composer php8.1-common php8.1-pgsql php8.1-fpm php8.1-redis php8.1-mbstring\
        php8.1-simplexml php8.1-bcmath php8.1-gd php8.1-curl php8.1-zip\
        php8.1-imagick php8.1-bz2 php8.1-gmp php8.1-int php8.1-pcov php8.1-soap php8.1-xsl\
        nodejs npm\
    && cp docker/*-wrapper.sh /usr/local/bin \
    && chmod a+x /usr/local/bin/*.sh /app/artisan \
    && useradd opnform \
    && cp .env.docker .env \
    && echo "daemon off;" >> /etc/nginx/nginx.conf\
    && cp docker/php-fpm.conf /etc/php/8.1/fpm/pool.d\
    && cp docker/nginx.conf /etc/nginx/sites-enabled/default\
    && cp docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf\
    && npm install && npm run build && rm -rf node_modules\
    && composer install\
    && echo "daemonize no" >> /etc/redis/redis.conf\
    && echo "appendonly yes" >> /etc/redis/redis.conf\
    && echo "dir /persist/redis/data" >> /etc/redis/redis.conf\
    && apt-get remove -y composer npm nodejs\
    && apt-get autoremove -y\
    && apt-get clean
    

EXPOSE 80
