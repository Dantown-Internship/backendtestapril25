# 🧾 Mid-Level Technical Test – Multi-Tenant SaaS-Based Expense Management API

Welcome! This is a technical test for laravel backend developers.

Your task is to build a secure, high-performance API for a **Multi-Tenant SaaS-based Expense Management System**, where multiple companies can manage their expenses independently. Please follow the instructions below and submit your solution as described.

---

## 🚀 Project Requirements

### ✅ Key Features to Implement

-   **Multi-Tenant Support** – Companies should have isolated data.
-   **Secure API Authentication** – Use Laravel Sanctum.
-   **Role-Based Access Control (RBAC)** – Admins, Managers, Employees.
-   **Advanced Query Optimization** – Indexing, Eager Loading.
-   **Background Job Processing** – Laravel Queues.
-   **Audit Logging** – Track changes to expenses.

---

## 🗂️ Tasks Breakdown

### 🏗️ Task 1: Multi-Tenant Database Structure (Migrations & Models)

#### Companies Table

-   Fields: `id`, `name`, `email`, `created_at`, `updated_at`

#### Users Table (Modified)

-   Add `company_id` (Foreign Key)
-   Add `role` (Enum: `["Admin", "Manager", "Employee"]`)

#### Expenses Table

-   Fields: `id`, `company_id`, `user_id`, `title`, `amount`, `category`, `created_at`, `updated_at`
-   Add an index on `company_id` for performance

#### Relationships

-   A **Company** has many **Users**
-   A **User** belongs to a **Company**
-   A **User** has many **Expenses**

---

### 🔐 Task 2: API Authentication & RBAC

-   Use **Laravel Sanctum** for token-based authentication
-   Implement Role-Based Access Control:
    -   **Admin**: Manage users & expenses
    -   **Manager**: Manage expenses (cannot delete users)
    -   **Employee**: View and create expenses
-   Ensure users **cannot access data** from other companies

---

### 🧾 Task 3: API Endpoints

#### Authentication

-   `POST /api/register` → Admin only
-   `POST /api/login`

#### Expense Management

-   `GET /api/expenses` → List (by company, paginated, searchable by title/category)
-   `POST /api/expenses` → Create (restricted to logged-in user’s company)
-   `PUT /api/expenses/{id}` → Update (Managers & Admins only)
-   `DELETE /api/expenses/{id}` → Delete (Admins only)

#### User Management

-   `GET /api/users` → List users (Admins only)
-   `POST /api/users` → Add user (Admins only)
-   `PUT /api/users/{id}` → Update user role (Admins only)

---

### ⚙️ Task 4: Optimization & Performance

-   Use **Eager Loading** (`with()`) to avoid N+1 queries
-   Add **indexes** on `company_id` and `user_id` in the expenses table
-   Implement **Redis caching** for frequently accessed queries

---

### 🧵 Task 5: Background Job Processing

-   Use Laravel Queues (with `database` or `redis` driver)
-   Create a **weekly job** that sends an expense report to all Admins
-   Use Laravel’s **scheduler** (`schedule:run`) to run the job

---

### 🕵️‍♀️ Task 6: Audit Logs

#### Audit Logs Table

-   Fields: `id`, `user_id`, `company_id`, `action`, `changes`, `created_at`

#### Requirements

-   Log every **update/delete** action on expenses
-   Store the **old and new values** of each expense before update

---

## 🛠️ Custom Features Implemented

In addition to fulfilling the core requirements of the multi-tenant SaaS-based expense management API, I have added several custom features to improve the functionality, structure, and performance of the application.

### Redis Caching with Predis

For optimized query performance, Redis is used as a caching layer for frequently accessed data. The **Predis** client is integrated into the Laravel application to facilitate Redis communication. By caching frequent queries (like lists of expenses or user details), the API reduces database load and improves response time for commonly requested data.

### Helper Functions for Reusable Code

To maintain cleaner and more efficient code, I have created a few helper functions that can be used throughout the application. These helpers simplify repetitive tasks and ensure consistency:

#### `audit_log` Helper Function

This function logs the changes made to any expense record (such as updates or deletions). It stores important information like the user who made the change, the company, the type of action (e.g., "update" or "delete"), and the old and new values of the resource:

```php
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('audit_log')) {
    function audit_log($action, $userId, $companyId, $resource, $old, $new)
    {
        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'action' => "{$action} {$resource}",
            'changes' => json_encode([
                'old' => $old,
                'new' => $new,
            ]),
        ]);
    }
}
```

#### `companyID` Helper Function

This function retrieves the `company_id` of the currently authenticated user. It ensures that only data relevant to the authenticated user's company is accessed.

```php
if (!function_exists('companyID')) {
    function companyID()
    {
        return Auth::check() ? Auth::user()->company_id : null;
    }
}
```

#### `userID` Helper Function

This function retrieves the `user_id` of the currently authenticated user, which is used for authorization and logging purposes.

```php
if (!function_exists('userID')) {
    function userID()
    {
        return Auth::check() ? Auth::user()->id : null;
    }
}
```

### Custom Response & Error Formatting

To standardize API responses and error handling across the application, I have implemented custom response and error formatting functions in the controllers.

#### `response` Function

This function formats all API responses consistently, making it easy to send success responses with optional metadata.

```php
public function response(string | array $message, ?array $meta = null, int $statusCode = 200)
{
    return response()->json([
        'message' => $message,
        'meta' => $meta,
    ], $statusCode);
}
```

#### `formatError` Function

This function formats validation errors to return a clean, flattened list of error messages, which is useful for the client-side to handle and display.

```php
public function formatError($validator)
{
    $errors = collect($validator->errors()->toArray())->flatten()->toArray();
    return $errors;
}
```

### Database Seeders

I have created database seeders to populate the database with initial test data for the **Companies**, **Users**, and **Expenses** tables. This helps in quickly testing the application in different scenarios without manually creating data.

### Unit Tests

I have implemented unit tests to ensure the application behaves as expected. These tests cover authentication, authorization, and functionality of the API endpoints. Each test is designed to validate the expected output and check if the appropriate responses and status codes are returned.

---

This section highlights the key additions and improvements beyond the basic functionality required by the task. These changes enhance performance, maintainability, and security, and they provide an easy way to manage common operations across the application.
