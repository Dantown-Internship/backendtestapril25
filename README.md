Project Overview
This project is a robust and secure Laravel-based application designed to handle user management, company registration, expense tracking, and role-based access control. It incorporates a modern tech stack to ensure scalability, performance, and maintainability.

Tech Stack
Backend Framework
Laravel – PHP framework used for routing, request handling, and core application logic.

Excel Integration
Maatwebsite Excel – Used for importing and exporting Excel files, making data handling and reporting seamless.

Caching & Performance
Redis – Utilized for caching, queue management, and performance optimization.

Database
PostgreSQL – A powerful, open-source object-relational database system for storing structured data securely and efficiently.

Authentication
Laravel Sanctum – Used for API authentication via tokens, enabling secure and stateless communication.

Testing
Feature Tests – Extensive test coverage using Laravel's built-in testing tools to ensure functionality and prevent regressions.

Authorization
Policies – Enforced user authorization logic to restrict access to sensitive operations such as updating user roles and managing resources within the same company.

Middleware
Custom and built-in Laravel middleware used to handle authentication, role verification, and request throttling to ensure secure and smooth user experience.

## Postman Documentation
```bash
https://documenter.getpostman.com/view/30858403/2sB2cd4xtK
```

## Project setup

```bash
cp .env.example .env
```

```bash
composer install
```
```bash
composer update
```
```bash
php artisan migrate
```
```bash
php artisan serve
```
## Open a new terminal and start the scheduler

```bash
php artisan config:clear
```
```bash
# development
php artisan schedule:work
```
## Open a new terminal and start the queue

```bash
php artisan config:clear
```
```bash
php artisan queue:work
```

## Run tests

```bash
# feature tests
php artisan test
