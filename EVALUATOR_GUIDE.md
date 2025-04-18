# Evaluator Guide - Expense Management API

This guide is specifically designed for evaluators to quickly assess the implementation of the Multi-Tenant SaaS-Based Expense Management API according to the evaluation criteria.

## 🔍 Quick Start

1. Clone the repository and install dependencies:
   ```bash
   git clone https://github.com/your-username/backendtestapril25.git
   cd backendtestapril25
   composer install
   ```

2. Configure and prepare the environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   # Configure database in .env file
   php artisan migrate --seed
   php artisan serve
   ```

3. Access API documentation:
   ```
   http://localhost:8000/docs
   ```

4. For background jobs, run in a separate terminal:
   ```bash
   php artisan queue:work
   ```

## 📋 Evaluation Criteria Checklist

### 1. Correctness & Completeness of Features

- **Multi-Tenant Support**
  - ✅ Companies Table: `database/migrations/2025_04_17_045703_create_companies_table.php`
  - ✅ Company-User Relationship: `app/Models/Company.php` and `app/Models/User.php`
  - ✅ Data Isolation: `app/Http/Middleware/EnsureCompanyAccess.php`

- **API Authentication**
  - ✅ Laravel Sanctum: Check `composer.json` and auth setup in `app/Http/Controllers/API/AuthController.php`
  - ✅ Login/Register: Test with `POST /api/login` and `POST /api/register`

- **Role-Based Access Control**
  - ✅ Role Enum: `app/Models/User.php` and user table migration
  - ✅ RBAC Middleware: `app/Http/Middleware/EnsureUserHasRole.php`
  - ✅ Authorization Policies: `app/Policies/UserPolicy.php` and `app/Policies/ExpensePolicy.php`

- **Required API Endpoints**
  - ✅ User Management: CRUD in `app/Http/Controllers/API/UserController.php`
  - ✅ Expense Management: CRUD in `app/Http/Controllers/API/ExpenseController.php`
  - ✅ Authentication: `app/Http/Controllers/API/AuthController.php`

- **Background Job Processing**
  - ✅ Weekly Report Job: `app/Jobs/WeeklyExpenseReportJob.php`
  - ✅ Scheduler Config: `app/Console/Kernel.php`

- **Audit Logging**
  - ✅ Audit Table: `database/migrations/2025_04_17_045849_create_audit_logs_table.php`
  - ✅ Audit Service: `app/Services/AuditLogger.php`
  - ✅ Event Observers: `app/Observers/ExpenseObserver.php`

### 2. Code Structure and Readability

- ✅ Laravel Directory Structure: Organized by feature and responsibility
- ✅ Consistent Naming Conventions: Controller methods, routes, variables
- ✅ Clean Code Practices: Dependency injection, single responsibility

### 3. Proper Use of Laravel Best Practices

- ✅ Eloquent Relationships: Check models
- ✅ Middleware for Access Control: Registered in `app/Http/Kernel.php`
- ✅ Request Validation: Form requests in `app/Http/Requests/`
- ✅ Policy-based Authorization: Applied in controllers

### 4. Security and Role Enforcement

- ✅ Tenant Isolation: Test by creating two companies
- ✅ Role Restrictions: Test by logging in as different roles
- ✅ Input Validation: All endpoints validate input

### 5. Performance Optimizations

- ✅ Database Indexes: Check `database/migrations/2025_04_17_112208_add_performance_indexes.php`
- ✅ Eager Loading: Used in controllers to prevent N+1 queries
- ✅ Redis Caching: Implemented for frequently accessed data

### 6. Bonus Features

- ✅ API Documentation: Scribe at `/docs`
- ✅ Tests: Run `php artisan test`
- ✅ Redis Integration: Check `.env` settings
- ✅ Proper API Responses: HTTP status codes and consistent formatting

## 🧪 Specific Test Scenarios

### 1. Test Multi-Tenant Isolation

**Objective**: Verify a user from one company cannot access data from another company.

1. Create two companies with separate users
2. Log in as a user from Company A
3. Try to access an expense from Company B
4. Expected: 403 Forbidden response

### 2. Test Role-Based Access

**Objective**: Verify different roles have appropriate permissions.

| Role      | Can View Expenses | Can Create Expenses | Can Update Expenses | Can Delete Expenses | Can Manage Users |
|-----------|-------------------|---------------------|---------------------|---------------------|------------------|
| Admin     | ✓                 | ✓                   | ✓                   | ✓                   | ✓                |
| Manager   | ✓                 | ✓                   | ✓                   | ✗                   | ✗                |
| Employee  | ✓                 | ✓                   | ✗                   | ✗                   | ✗                |

### 3. Test Background Job Processing

**Objective**: Verify weekly expense reports are generated.

1. Check scheduled tasks:
   ```bash
   php artisan schedule:list
   ```
   Verify that the weekly expense report job is scheduled.

2. Run the scheduler manually:
   ```bash
   php artisan schedule:run
   ```

3. Or dispatch the job directly:
   ```bash
   php artisan app:dispatch-weekly-report-job
   ```

4. Check logs or mail trap for the sent email
5. Expected: Email sent to company admins

### 4. Test Audit Logging

**Objective**: Verify expense changes are logged.

1. Create, update, and delete expenses through the API
2. Check the audit logs using one of these methods:

   **Method 1**: Via Tinker
   ```bash
   php artisan tinker
   # In tinker
   App\Models\AuditLog::latest()->take(10)->get();
   App\Models\AuditLog::latest()->first();
   ```

   **Method 2**: Via API
   ```
   GET /api/audit-logs
   ```

3. Expected: Entries showing:
   - User who made the change
   - Company ID 
   - Action type (create, update, delete)
   - Changes in JSON format with old and new values

### 5. Test Redis Cache Performance

**Objective**: Verify Redis caching improves performance.

1. Start Redis monitoring:
   ```bash
   redis-cli monitor
   ```

2. Make API requests to cache-enabled endpoints:
   ```
   GET /api/expenses
   ```

3. Observe in Redis monitor:
   - First request: Cache misses (data being stored)
   - Subsequent requests: Cache hits (faster responses)

4. Test cache invalidation:
   ```
   PUT /api/expenses/{id}
   ```
   Then observe cache keys being deleted in Redis monitor.

5. Check cache statistics:
   ```bash
   redis-cli info stats | grep cache
   ```

## 📞 Contact

If you encounter any issues or have questions during evaluation, please contact: chukwuebuka.nwaforx@gmail.com