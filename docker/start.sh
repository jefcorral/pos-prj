#!/bin/sh
set -e

# Render assigns the port via $PORT (default 10000)
sed "s/__PORT__/${PORT:-10000}/" \
    /etc/nginx/http.d/default.conf.template \
    > /etc/nginx/http.d/default.conf

php artisan config:cache
php artisan migrate --force
php artisan db:seed --force

php-fpm -D
exec nginx -g 'daemon off;'
