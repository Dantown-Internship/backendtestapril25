# Multi-Tenant Expense Management System

## ✅ Implementation Summary
### Core Requirements Fulfilled
- **Multi-tenancy**: Full company data isolation
- **Authentication**: Sanctum tokens + RBAC (Admin/Manager/Employee)
- **API Endpoints**: Complete CRUD for expenses/users
- **Automated Reports**: Weekly email dispatch via queues
- **Audit Logs**: Track all expense changes

### Additional Features
- Test data seeders (`TestCompanySeeder`)
- Papercut email testing integration
- Query optimization (eager loading, indexes)

## 🛠️ Setup Guide
```bash
# Clone & install

composer install
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed


Testing
Credentials (Password: password)
Role	                 Email
Admin	            admin_a@example.com
Manager	            manager_a@example.com
Employee	        employee_a@example.com

Running Tests
# Start services
php artisan serve
php artisan queue:work

# Manual test
php artisan tinker
>>> dispatch(new App\Jobs\SendExpenseReport);

📡 API Documentation
Base URL: http://127.0.0.1:8000/api

Key Endpoints
Method	                Endpoint	                    Access Control
POST	                /login	                        All
GET	                    /expenses	                    Manager+
POST	                /employee/expenses	            Employee+

Security
Rate Limits:

Auth: 5/min

API: 60/min

Middleware: Sanctum tokens + role checks

-   ***Configuration****

# .env
QUEUE_CONNECTION=database  # Used instead of Redis
CACHE_DRIVER=file          # Fallback caching
