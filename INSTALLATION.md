# Detailed Installation & Testing Guide

This document provides comprehensive instructions for setting up, configuring, and testing the Multi-Tenant SaaS-Based Expense Management API.

## NOTE: After installation is complete, go to backendtestapril25/EVALUATOR_GUIDE.md 
## 📋 Prerequisites

- **PHP 8.1+**
- **Composer**
- **MySQL or PostgreSQL**
- **Redis** (for caching and queue processing)

## 🔧 Step-by-Step Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/backendtestapril25.git
cd backendtestapril25
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration

Edit the `.env` file and configure your database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_management
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Redis Configuration (Recommended)

For optimal performance, configure Redis in the `.env` file:

```
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

If Redis is not available, you can use the database driver for queues:

```
QUEUE_CONNECTION=database
```

### 6. Database Setup

Run migrations to create the database tables:

```bash
php artisan migrate
```

### 7. Seed the Database with Test Data

```bash
php artisan db:seed
```
Note: we have 3 roles: Admin, Manager and Employee 
This creates:
- A test company
- Admin user/role (email: admin@example.com, password: password)
- Manager user/role (email: manager@example.com, password: password)
- Employee user/role (email: employee@example.com, password: password)
- Sample expenses

### 8. Start the Development Server

```bash
php artisan serve
```

The API will now be available at `http://localhost:8000`

### 9. API Documentation

Scribe API documentation is available at:
- http://localhost:8000/docs

### 10. Queue Worker (Required for Background Jobs)

In a separate terminal, run the queue worker:

```bash
php artisan queue:work
```

Keep this running for background jobs to process.

## 🧪 Testing the API

### Authentication

1. **Register a new company** (first user becomes admin):
   ```
   POST /api/register
   {
     "name": "John Doe",
     "email": "john@example.com",
     "password": "password",
     "company_name": "ACME Inc",
     "company_email": "info@acme.com"
   }
   ```

2. **Login to get token**:
   ```
   POST /api/login
   {
     "email": "john@example.com",
     "password": "password"
   }
   ```

3. **Use the token in subsequent requests**:
   ```
   Authorization: Bearer YOUR_TOKEN_HERE
   ```

### Testing Multi-Tenant Isolation

1. Create two companies with different users
2. Log in as a user from Company A and try to access Company B's expenses
3. Verify that access is denied

### Testing Role-Based Access Control

1. **Admin user**: Can manage users and expenses
2. **Manager user**: Can manage expenses but not users
3. **Employee user**: Can only view and create expenses

### Testing Background Jobs

To test the weekly expense report job:

```bash
php artisan app:dispatch-weekly-report-job
```

This will queue the job, which will be processed by the queue worker.

To verify that the Laravel Scheduler has been properly configured:

```bash
php artisan schedule:list
```

This will show all scheduled tasks, including our weekly expense report job.

To manually run all scheduled tasks as if it was the right time:

```bash
php artisan schedule:run
```

### Testing Audit Logging

To verify audit logging is working correctly:

1. Create, update, or delete an expense through the API
2. Check the audit logs using one of these methods:

**Method 1**: Using Laravel Tinker
```bash
php artisan tinker
```

Then within Tinker, run:
```php
App\Models\AuditLog::latest()->take(10)->get();   // Get 10 most recent logs
App\Models\AuditLog::latest()->first();           // Get the most recent log
```

**Method 2**: Using the API endpoint
```
GET /api/audit-logs
```

You should see entries with the following data:
- `user_id`: The user who made the change
- `company_id`: The company the expense belongs to
- `action`: The type of change (create, update, delete)
- `changes`: JSON with old and new values

### Testing Query Optimization

1. Check eager loading with:
   ```
   GET /api/eager-loading-test
   ```
2. Compare with:
   ```
   GET /api/test-eager-loading
   ```

The first endpoint uses eager loading to prevent N+1 queries.

### Testing Redis Cache Performance

To test Redis caching performance and verify it's working:

1. Install Redis CLI tools if not already installed:
   ```bash
   sudo apt-get install redis-tools
   ```

2. Monitor Redis operations in real-time:
   ```bash
   redis-cli monitor
   ```

3. In another terminal, make several API requests to endpoints that use caching:
   ```
   GET /api/expenses
   ```

4. In the Redis monitor, you should see:
   - First request: Cache miss and storage operations
   - Subsequent requests: Cache hits

5. To test cache invalidation, update an expense:
   ```
   PUT /api/expenses/{id}
   ```
   
   Then check Redis monitor - you should see cache keys being deleted.

6. For cache hit/miss statistics, run:
   ```bash
   redis-cli info stats | grep cache
   ```

## 🔍 Troubleshooting

### Common Issues

1. **"Class not found" errors**:
   - Run `composer dump-autoload`

2. **Database connection errors**:
   - Verify your `.env` database settings
   - Make sure MySQL/PostgreSQL is running

3. **Redis connection errors**:
   - Verify Redis is installed and running
   - Check Redis connection settings in `.env`

4. **Queue not processing**:
   - Ensure queue worker is running: `php artisan queue:work`
   - Check for failed jobs: `php artisan queue:failed`

5. **API returns 500 errors**:
   - Check storage/logs/laravel.log for error details

## 📋 Evaluation Checklist

Use this checklist to verify that all required features are working:

- [ ] API Authentication with Sanctum
- [ ] Multi-Tenant data isolation
- [ ] Role-based access control
- [ ] User management API (Admin only)
- [ ] Expense management API
- [ ] Background job processing
- [ ] Audit logging for expenses
- [ ] Query optimization and caching
- [ ] API documentation

## 🏆 Bonus Features

The application also includes these bonus features:

- API documentation with Scribe
- Redis integration
- Performance optimizations with eager loading and indexing
- Comprehensive test suite 