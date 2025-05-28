#!/bin/bash

# Run database migrations
php artisan migrate --seed

# Start php-fpm
php-fpm -y /usr/local/etc/php-fpm.conf &

# Start nginx
nginx -g "daemon off;" &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?