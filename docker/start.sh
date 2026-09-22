#!/bin/sh
set -e

# Render assigns the port via $PORT (default 10000)
sed "s/__PORT__/${PORT:-10000}/" \
    /etc/nginx/http.d/default.conf.template \
    > /etc/nginx/http.d/default.conf

# SQLite demo database (ephemeral — rebuilt on every boot)
touch database/database.sqlite
chown -R www-data:www-data database

# Always use a valid base64 key (Render's generated value isn't guaranteed valid)
export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"

# Run artisan as www-data so all caches and the db stay writable by php-fpm
su -s /bin/sh www-data -c 'php artisan config:cache'
su -s /bin/sh www-data -c 'php artisan migrate --force'
su -s /bin/sh www-data -c 'php artisan db:seed --force'

php-fpm -D
exec nginx -g 'daemon off;'
