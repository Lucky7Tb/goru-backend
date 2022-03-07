# Goru
Aplikasi yang bertujuan untuk mencari guru les terbaik untuk anak SD - SMA.

## Requirements
1. Min PHP 8.1 
2. Mysql / Postgress

## Installation
1. Clone this project
2. `cd /path/to/this/project`
3. `composer install`
4. `cp .env.example`
5. `php artisan key:generate`
6. Config database to your local database configuration
7. If you using v-host set .env var `APP_URL` and `SESSION_DOMAIN` to your v-host. Otherwise just set to localhost
8. `php artisan migration --seed`
