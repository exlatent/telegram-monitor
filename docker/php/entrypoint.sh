#!/bin/sh
set -e

# Fix permissions
chmod -R 755 /var/www/html/views
chmod -R 755 /var/www/html/config
chmod -R 755 /var/www/html/src
chmod -R 777 /var/www/html/runtime
chmod -R 777 /var/www/html/web/assets

echo "Running composer install..."
composer install --no-interaction --prefer-dist --optimize-autoloader

exec "$@"