#!/bin/bash

set -e

# Function to wait for the database to be ready
wait_for_db() {
  echo "Waiting for MySQL to start..."
  while ! nc -z mysql 3306; do
    sleep 0.1
  done
  echo "MySQL started"
}

# Wait for the database
wait_for_db

# Execute migrations
echo "Running migrations..."
php artisan migrate --force

echo "Том /var/www пуст, копируем файлы из /var/www-src..."
rm -rf /var/www/*
cp -r /var/www-src/* /var/www/
rm -rf /var/www-src/*
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

cd /var/www
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Execute the initial command (e.g. php-fpm)
exec "$@"
