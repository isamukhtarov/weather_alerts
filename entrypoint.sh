#!/bin/bash

set -e

until nc -z -v -w30 db 3306; do
  sleep 2
done

composer install

if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install

php artisan key:generate

php artisan migrate:fresh --seed

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear


npm install
npm run build

nohup php artisan queue:work > /dev/null 2>&1 &
nohup php artisan schedule:work > /dev/null 2>&1 &

php artisan test --filter=WeatherTest || true

exec php-fpm

