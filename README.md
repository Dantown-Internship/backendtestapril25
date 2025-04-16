# IbidapoExpense - Multi-Tenant Expense Management System

<p align="center">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## Overview

IbidapoExpense is a secure, high-performance API for a Multi-Tenant SaaS-based Expense Management System. It allows multiple companies to manage their expenses independently while maintaining complete data isolation.

## Key Features

- **Multi-Tenant Architecture**
  - Complete data isolation between companies
  - Company-specific user management
  - Secure data access controls
  - Automatic company scoping middleware

- **Role-Based Access Control (RBAC)**
  - Admin: Full system access and user management
  - Manager: Expense management and approval
  - Employee: Create and view own expenses
  - Granular permission policies

- **Expense Management**
  - Create, read, update, and delete expenses
  - Categorized expenses with detailed tracking
  - Expense summaries and analytics
  - Search and filter capabilities
  - Form request validation

- **Performance Optimizations**
  - Redis caching for frequently accessed data
  - Database indexing for improved query performance
  - Eager loading to prevent N+1 queries
  - Query optimization and scoping

- **Security Features**
  - Laravel Sanctum for API authentication
  - Password hashing and validation
  - Role-based authorization
  - Company data isolation
  - Form request validation
  - Audit logging

- **Automated Features**
  - Weekly expense reports
  - Background job processing
  - Automated email notifications
  - Scheduled tasks

- **Audit System**
  - Comprehensive change tracking
  - User action logging
  - Data modification history
  - Cached audit logs

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Redis (required for caching and queues)
- Laravel 12

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/ibidapoexpense.git
cd ibidapoexpense
```

2. Install dependencies:
```bash
composer install
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database and Redis in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ibidapoexpense
DB_USERNAME=your_username
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```

6. Run migrations and seeders:
```bash
php artisan migrate --seed
```

7. Start the queue worker:
```bash
php artisan queue:work
```

## API Documentation

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Response:
```json
{
    "token": "your-api-token"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer your-api-token
```

#### Register (Admin Only)
```http
POST /api/register
Content-Type: application/json
Authorization: Bearer your-api-token

{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "employee"
}
```

### Expenses

#### List Expenses
```http
GET /api/expenses
Authorization: Bearer your-api-token
```

Query Parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15)
- `search`: Search term (searches in title and category)
- `start_date`: Filter by start date
- `end_date`: Filter by end date

Access Control:
- All authenticated users can list expenses
- Results are automatically scoped to the user's company
- Employees see only their own expenses
- Managers and Admins see all company expenses

#### Get Expense Summary
```http
GET /api/expenses/summary
Authorization: Bearer your-api-token
```

Access Control:
- All authenticated users can view expense summaries
- Data is scoped to the user's company

#### Get Single Expense
```http
GET /api/expenses/{id}
Authorization: Bearer your-api-token
```

Access Control:
- Employees can view only their own expenses
- Managers and Admins can view any expense in their company

#### Create Expense
```http
POST /api/expenses
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "title": "Office Supplies",
    "amount": 100.50,
    "category": "office",
    "date": "2024-04-16" // optional, defaults to current date
}
```

Access Control:
- All authenticated users can create expenses
- Expenses are automatically associated with the user's company

#### Update Expense (Managers & Admins Only)
```http
PUT /api/expenses/{id}
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "title": "Updated Title",
    "amount": 150.75,
    "category": "travel",
    "date": "2024-04-16"
}
```

Access Control:
- Only Managers and Admins can update expenses
- Must be within the same company

#### Delete Expense (Admins Only)
```http
DELETE /api/expenses/{id}
Authorization: Bearer your-api-token
```

Access Control:
- Only Admins can delete expenses
- Must be within the same company

### Users

#### List Users (Admins Only)
```http
GET /api/users
Authorization: Bearer your-api-token
```

Query Parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15)
- `search`: Search term (searches in name and email)
- `role`: Filter by role (Admin, Manager, Employee)

Access Control:
- Only Admins can list users
- Results are automatically scoped to the admin's company

#### Update User Password
```http
PUT /api/users/password
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "current_password": "current_password",
    "password": "new_password",
    "password_confirmation": "new_password"
}
```

Access Control:
- All authenticated users can update their own password

#### Update User (Admins Only)
```http
PUT /api/users/{id}
Authorization: Bearer your-api-token
Content-Type: application/json

{
    "name": "Updated Name",
    "email": "updated@example.com",
    "role": "manager"
}
```

Access Control:
- Only Admins can update users
- Must be within the same company

### Audit Logs

#### List Audit Logs
```http
GET /api/audit-logs
Authorization: Bearer your-api-token
```

Query Parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15)
- `search`: Search term
- `user_id`: Filter by user
- `model_type`: Filter by model type

Access Control:
- All authenticated users can view audit logs
- Results are automatically scoped to the user's company

#### Get Single Audit Log
```http
GET /api/audit-logs/{id}
Authorization: Bearer your-api-token
```

Access Control:
- All authenticated users can view audit logs
- Must be within the same company

#### Clear Company Audit Logs Cache (Admins Only)
```http
POST /api/audit-logs/{company}/clear-cache
Authorization: Bearer your-api-token
```

Access Control:
- Only Admins can clear audit log caches
- Must be within the same company

## Security Notes

- All API endpoints (except login) require authentication using Bearer token
- The register endpoint is only accessible to admin users
- All requests are automatically scoped to the user's company
- Password updates require current password verification
- Audit logs track all significant changes to the system
- Role-based access control is enforced at both route and request levels
- Company data isolation is maintained through middleware and explicit checks

## Background Jobs

The system uses Laravel's queue system for processing background jobs:

1. Weekly Expense Reports
   - Generated every Monday at 9:00 AM
   - Sent to company admins
   - Includes expense summaries and analytics

2. Audit Log Processing
   - Asynchronous logging of changes
   - Cached for performance
   - Automatic cleanup of old logs

## Performance Optimizations

- Redis caching for frequently accessed data
- Database indexing on commonly queried columns
- Eager loading to prevent N+1 queries
- Query optimization through scopes
- Background job processing for heavy operations
- Cached audit logs and reports

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
