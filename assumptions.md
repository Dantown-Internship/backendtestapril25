## PR Submission - Multi-Tenant Expense Manager API

**Full Name:** Alexander Igonor

---

### Features Implemented

1. **Authentication**
   - API authentication using Laravel Sanctum via Laravel Breeze
   - Role-based access control (`Admin`, `Manager`, `Employee`)
   - Admins manage users within their company; registration/login via Breeze Auth controllers

2. **User Management**
   - Admins can list, create, assign roles, and soft delete users (internally scoped by company)
   - User listing results are cached per company using Redis

lright3. **Expense Management**
   - CRUD operations on expenses, scoped by company and user
   - Form Requests (`StoreExpenseRequest`, etc.) used for validation (SRP-compliant)
   - Eager loaded relationships for `category` and `user` to prevent N+1 queries
   - Pagination and search implemented
   - Results cached per search term, company, and page using Redis

4. **Optimization & Performance**
   - **Redis** caching for frequently accessed queries (user list, expense list)
   - **Eager Loading** used for expense listings (`with('user', 'category')`)
   - **Indexing** added to `expenses` table for `user_id` and `company_id` fields

5. **Background Jobs (Task 5)**
   - Weekly job (`SendWeeklyExpenseReport`) sends a PDF summary to Admins
   - Scheduler runs weekly (configured to `everyMinute()` for testing)
   - Uses Laravel queue with `database` driver
   - Logs job success/failure in `storage/logs/laravel.log`

6. **Audit Logs (Task 6)**
   - Captures old and new values for `update` and `delete` actions on expenses
   - Stored in a separate `audit_logs` table
   - Logs who made the change and when

---

### Instructions for Testing

> Ensure `.env` is properly configured for `DB_*`, `CACHE_DRIVER=redis`, and `QUEUE_CONNECTION=database`

1. **Seeder & Initial Setup**
   - Run migrations and seeders:
     ```
     php artisan migrate:fresh --seed
     ```
   - Seeders populate:
     - Roles: `Admin`, `Manager`, `Employee`
     - Companies
     - Users (with various roles)
     - Categories
     - Expenses (linked to companies and users)

2. **Authentication**
   - Test registration:  
     `POST /api/register`  
     Body: `name`, `email`, `password`, `password_confirmation`
   - Test login:  
     `POST /api/login`  
     Body: `email`, `password`  
     > Returns token (use as Bearer Token in Postman for subsequent requests)

3. **User Management**
   - Get users (Admin only):  
     `GET /api/users`  
   - Create new user (Admin only):  
     `POST /api/users`  
     Body: `name`, `email`, `password`, `role`
   - Update user role:  
     `PUT /api/users/{user}/role`  
   - Soft delete user:  
     `DELETE /api/users/{user}`

4. **Expense Management**
   - List expenses (with pagination & search):  
     `GET /api/expenses?page=1&search=transport`
   - Create new expense:  
     `POST /api/expenses`  
     Body: `title`, `amount`, `category_id`
   - Update expense:  
     `PUT /api/expenses/{id}`  
   - Delete expense:  
     `DELETE /api/expenses/{id}`  
   - Validate caching: repeat listing endpoints to observe faster response

5. **Audit Logging**
   - After updating or deleting an expense, check the `audit_logs` table:
     ```
     select * from audit_logs order by created_at desc;
     ```

6. **Redis Caching**
   - Redis caching was implemented; queue connection changed from `database` to `redis`.
   - Confirm Redis is running (`redis-server`)
   - Caching is used on:
     - User listings by company
     - Expense listings by search term, page, and company
   - Clear cache:
     ```
     php artisan cache:clear
     ```

7. **Queue & Scheduled Jobs**
   - Start queue worker:
     ```
     php artisan queue:work
     ```
   - Trigger scheduled job manually (for testing):
     ```
     php artisan schedule:run
     ```
   - Observe PDF report log in:
     ```
     storage/logs/laravel.log
     ```

---

### Notes / Assumptions

- **Redis** integrated as primary cache driver to improve performance of expense and user queries
- I chose to use **Laravel Breeze** for user registration, login, and Sanctum token auth with minimal setup — it uses `Auth\` controllers and adheres to Laravel best practices
- I used **UserController** to handle internal company user management by Admins (list, create, assign roles)
- Breeze handles public-facing registration/login, while Admins handle internal staff via UserController
- I used **Form Requests** (e.g., `StoreExpenseRequest`) to separate validation logic from controllers — adheres to SRP and keeps controllers clean
- I chose to use `category_id` instead of a string field — categories are stored in a normalized `categories` table. This ensures referential integrity, enables filtering, and avoids duplication
- Audit logs are not yet exposed via API (can be added if required)
- Email sending for weekly reports uses Laravel’s default `log` mail driver (no actual mail sent)
- PDF report uses simple HTML layout for clear and fast rendering
- All emails in the seeded data are fake and used only for development/testing

---

### Features Skipped (with Reason)

- **Automated tests**: Not implemented due to time constraints, though code is test-read and testing with POSTMAN.