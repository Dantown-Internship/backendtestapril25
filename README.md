# Expense Management API

A comprehensive API for expense management built with Laravel 12, featuring role-based access control and company segregation of data.

## Features

- **Authentication**: Secure authentication using Laravel Sanctum
- **Role-Based Access Control**: Admin, Manager, and Employee roles with appropriate permissions
- **Company Data Isolation**: Users can only access data within their own company through authorization policies
- **Audit Logging**: Automatic tracking of all changes to expenses
- **Weekly Reports**: Automated weekly expense reports sent to company administrators

## Core Components

- **Resources**: API resources for Expenses, Users, and Companies
- **Policies**: Authorization policies for fine-grained access control
- **Traits**: Auditable trait for tracking changes
- **Jobs**: Weekly expense report generation and delivery
- **Blade Templates**: Email templates for weekly reports

## Run Locally

- Clone the project

```bash
git clone https://github.com/username/backendtestapril25.git
```

- Go to the project directory

```bash
cd expense-management-api
```

- Install composer dependencies

```bash
composer install
```

- Set up an empty database and configure .env file with your database credentials

- Run migrations

```bash
php artisan migrate
```

- Start the server

```bash
php artisan serve
```

## Scheduled Tasks

The application includes a scheduled task that sends weekly expense reports to company administrators:

```bash
php artisan schedule:run
```

## Documentation
The postman documentation for the endpoints can be found here -

[Documentation](https://documenter.getpostman.com/view/18515005/2sB2cd3dR6)

