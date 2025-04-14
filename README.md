<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Expense Management Project

This project is developed using Laravel framework. You can install composer and run the migrations.

Below is my env file content
APP_NAME=ExpenseManagementSystem
APP_ENV=local
APP_KEY=base64:XQFtEdvQlv+dG4sDaQohmz2JWyIM1NXWa5wUGCP5Ghg=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_mgt
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"


## Creating Jobs table queue
php artisan queue:table
php artisan migrate

## Starting the queue worker
php artisan queue:work

## Starting the scheduler
php artisan schedule:work

## Testing the API's on postman

Below are demo accounts already created, you can run the php artisan db:seeder
ADMIN
'email' => admin@dantownhr.com
'password' => admin123

MANAGER
'email' => manager@dantownhr.com
'password' => manager123

EMPLOYEE
'email' => employee@dantownhr.com
'password' => employee123

ENDPOINTS
Login - POST: /api/login
    Headers: Content-Type: application/json, Accept: application/json
    Request body (JSON)
    {
        "email": "admin@example.com",
        "password": "password"
    }

Logout - POST /api/logout
    Headers: Authorization: Bearer your_token_here, Accept: application/json

List users (Admin only) - GET /api/users
    Headers: Authorization: Bearer your_token_here, Accept: application/json

Create users (Admin only) - POST /api/users
    Headers: Authorization: Bearer your_token_here, Content-Type: application/json, Accept: application/json
    Request body (JSON)
    {
        "name": "New Employee",
        "email": "employee@example.com",
        "password": "secret123",
        "role": "Employee"
    }

Update user (Admin only) - PUT /api/users/{id}
    Headers: Authorization: Bearer your_token_here, Content-Type: application/json, Accept: application/json
    Request body (JSON)
    {
        "role": "Manager"
    }

Create expense - POST /api/expenses
    Headers: Authorization: Bearer your_token_here, Content-Type: application/json, Accept: application/json
    Request body (JSON)
    {
        "title": "Team Lunch",
        "amount": 85.75,
        "category": "Meals"
    }

List expenses - GET /api/expenses
    Headers: Authorization: Bearer your_token_here, Accept: application/json

