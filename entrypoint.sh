#!/bin/bash

set -e

until nc -z -v -w30 db 3306; do
  sleep 2
done

composer install

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan migrate:fresh --seed

npm install
npm run build

nohup php artisan queue:work > /dev/null 2>&1 &
nohup php artisan schedule:work > /dev/null 2>&1 &

exec php-fpm

