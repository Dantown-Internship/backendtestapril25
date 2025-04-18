# Performance & Feature Testing Guide

This guide provides detailed instructions for testing specific features of the Multi-Tenant SaaS-Based Expense Management API, including scheduler configuration, audit logging, and Redis cache performance.

## 🕒 Testing Scheduler Configuration

The application uses Laravel's scheduler to run background jobs at specified intervals, particularly the weekly expense report.

### Viewing Scheduled Tasks

To see all tasks scheduled in the application:

```bash
php artisan schedule:list
```

Expected output should include our weekly expense report job:

```
+------------------------+------------------+-------------+
| Command                | Schedule         | Description |
+------------------------+------------------+-------------+
| weekly:expense-report  | 0 0 * * 0        | Weekly...   |
+------------------------+------------------+-------------+
```

### Testing Scheduler Execution

To manually trigger the scheduler as if it were running at the scheduled time:

```bash
php artisan schedule:run
```

### Testing the Weekly Report Job Directly

To bypass the scheduler and directly test the weekly report job:

```bash
php artisan app:dispatch-weekly-report-job
```

### Verifying Job Execution

1. Ensure the queue worker is running:
   ```bash
   php artisan queue:work
   ```

2. Check for successful job processing in the terminal running the queue worker

3. Check for emails in your configured mail trap or logs:
   ```bash
   tail -f storage/logs/laravel.log | grep "Weekly Expense Report"
   ```

4. Check the jobs table in the database to confirm job completion:
   ```bash
   php artisan tinker
   # In Tinker
   DB::table('jobs')->count();  # Should decrease after processing
   ```

## 📝 Testing Audit Logging

The application logs all changes to expenses in the audit_logs table for accountability and tracking.

### Generating Audit Log Entries

1. Login as an Admin or Manager:
   ```
   POST /api/login
   {
     "email": "admin@example.com",
     "password": "password"
   }
   ```

2. Create a new expense:
   ```
   POST /api/expenses
   {
     "title": "Test Expense for Audit",
     "amount": 100.00,
     "category": "Office Supplies"
   }
   ```

3. Update the expense:
   ```
   PUT /api/expenses/{id}
   {
     "title": "Updated Test Expense",
     "amount": 150.00,
     "category": "Office Supplies"
   }
   ```

4. Delete the expense:
   ```
   DELETE /api/expenses/{id}
   ```

### Viewing Audit Logs

#### Method 1: Using Laravel Tinker

```bash
php artisan tinker
```

Then run one of these commands:

```php
// View all audit logs
App\Models\AuditLog::all();

// Get the 10 most recent logs
App\Models\AuditLog::latest()->take(10)->get();

// Get the most recent log
App\Models\AuditLog::latest()->first();

// Filter logs by action type
App\Models\AuditLog::where('action', 'update')->get();

// View logs for a specific user
App\Models\AuditLog::where('user_id', 1)->get();

// View logs for a specific company
App\Models\AuditLog::where('company_id', 1)->get();
```

#### Method 2: Using the API

```
GET /api/audit-logs
```

You can also filter the logs:

```
GET /api/audit-logs?action=update
GET /api/audit-logs?user_id=1
```

### Examining Audit Log Structure

Each audit log entry should contain:

1. `user_id` - The ID of the user who made the change
2. `company_id` - The ID of the company the expense belongs to
3. `action` - The type of action (create, update, delete)
4. `changes` - JSON containing:
   - For creates: New values
   - For updates: Old and new values
   - For deletes: Old values
5. `created_at` - Timestamp of when the action occurred

Example of viewing the changes field:

```php
// In Tinker
$log = App\Models\AuditLog::latest()->first();
json_decode($log->changes);
```

## ⚡ Testing Redis Cache Performance

This section guides you through testing the Redis caching implementation and verifying its performance benefits.

### Prerequisites

1. Ensure Redis is running:
   ```bash
   redis-cli ping
   ```
   Should return `PONG`

2. Ensure Redis is configured in your .env file:
   ```
   CACHE_DRIVER=redis
   ```

### Monitoring Redis Operations

To see all Redis operations in real-time:

```bash
redis-cli monitor
```

Keep this running in a terminal window while you perform API requests.

### Testing Cache Hits and Misses

1. Clear the Redis cache:
   ```bash
   redis-cli flushall
   ```

2. Make your first request to a cached endpoint:
   ```
   GET /api/expenses
   ```

3. In the Redis monitor terminal, you should see `SETEX` commands for storing data

4. Make the same request again:
   ```
   GET /api/expenses
   ```

5. Now you should see `GET` commands retrieving cached data

6. Note the response time difference between the first and subsequent requests

### Testing Cache Invalidation

1. Update an expense:
   ```
   PUT /api/expenses/{id}
   {
     "title": "Cache Invalidation Test"
   }
   ```

2. In the Redis monitor, you should see `DEL` or `UNLINK` commands removing cached data

3. Make another request to view expenses:
   ```
   GET /api/expenses
   ```

4. You should see `SETEX` commands again, indicating cache rebuilding

### Checking Cache Statistics

```bash
redis-cli info stats | grep cache
```

Look for:
- `keyspace_hits`: Number of successful lookups of keys in the main dictionary
- `keyspace_misses`: Number of failed lookups of keys in the main dictionary
- `hit_rate`: Hit rate = keyspace_hits / (keyspace_hits + keyspace_misses)

### Comparing Performance

To compare performance with and without caching:

1. With Redis caching enabled, time a request:
   ```bash
   time curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/expenses
   ```

2. Disable Redis temporarily in .env:
   ```
   CACHE_DRIVER=array
   ```

3. Clear config cache:
   ```bash
   php artisan config:clear
   ```

4. Time the request again:
   ```bash
   time curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/expenses
   ```

5. Compare the real/user/sys times

## 📊 Performance Analysis

After testing, you should observe:

1. **Audit Logging**:
   - All create/update/delete operations on expenses are logged
   - Log entries contain correct user, company, action, and changes
   - No performance degradation from the logging

2. **Redis Caching**:
   - First request is slower (cache miss)
   - Subsequent requests are faster (cache hits)
   - Updates properly invalidate relevant caches
   - Overall system performance improves with caching

3. **Background Jobs**:
   - Jobs are properly queued
   - Scheduler correctly schedules jobs
   - Jobs process without errors
   - System remains responsive during job processing 