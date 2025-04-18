## Nsikanabasi Anthony Idung

### 🧾 Mid-Level Technical Test – Multi-Tenant SaaS-Based Expense Management API

#### Notes and Assumptions

* You have or can create your .env file comfortably
* You use a cache driver of either Redis, Memcached or Database
* The email environment needs careful setup (I used Google SMTP during development)

#### Features Implemented

* Laravel 12.x
* MySQL db
* Laravel Sanctum in Authentication with throttling (rate-limiter)
* Redis support (predis)
* Cache system that works for multiple cache drivers (redis, memcached, database)
* Laravel Queues & Scheduler
* Pest Test Suite
* Correct API endpoints
* **Multi-Tenant Support** – Companies have isolated data.
* **Role-Based Access Control (RBAC)** – Admins, Managers, Employees.
* **Advanced Query Optimization** – Indexing, Eager Loading.
* **Background Job Processing** – Laravel Queues.
* **Audit Logging** – Track changes to expenses.

#### Features Skipped

No feature was skipped

#### Instructions for Testing

* To make migrations and seeds for the testing environment - always use the --env=testing flag (eg.  php artisan db:seed --env=testing)
* If any test fails, (except the [WeeklyReportMailTest](tests\Feature\WeeklyReportMailTest.php)), simply reset the tests db and try again.

---
