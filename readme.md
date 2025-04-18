## Expense Management API Documentation

### 1. Introduction

This document provides a comprehensive overview of the Expense Management API, built with Laravel.

This API allows for the management of expenses across multiple companies, with support for user roles, authentication, and audit logging.

Passwords are hashed, auth endpoints are throttled, tests are available and worked when run

#### 🛠️ Tech Stack

- Laravel 12.x
- MySQL db
- Laravel Sanctum
- Redis (predis)
- Laravel Queues & Scheduler
- Pest Test Suite

### 2. Base URL

All API endpoints are relative to the following base URL:

```json
/api

```

### 3. Authentication

The API uses Laravel Sanctum for authentication. All protected routes require a valid Sanctum API token to be included in the `Authorization` header as a Bearer token.

### 4. User Roles

The API defines the following user roles using an Enum:

* **Admin** : Has full access to manage users and expenses for their company.
* **Manager** : Can manage expenses for their company.
* **Employee** : Can view and create expenses for their company.

### 5. API Endpoints

#### 5.1 Authentication

* **`POST /register`**
  * Description: Register a new user (Admin only).
  * Roles: Admin
  * Request:
    ```json
    {
        "name": "string",
        "email": "string (email, unique)",
        "password": "string (min: 8, confirmed)",
        "company_id": "integer (exists: companies,id)",
        "role": "string (enum: Administrator, Manager, Employee)"
    }

    ```
  * Response:
    ```json
    {
        "access_token": "string",
        "token_type": "string"
    }

    ```
  * Status Codes:
    * 200 OK
    * 422 Unprocessable Content (Validation errors)
    * 403 Forbidden (Unauthorized - non-Admin)
    * 429 Too Many Requests (Rate Limiting)
* **`POST /login`**
  * Description: Log in and get an API token.
  * Roles: Any
  * Request:
    ```json
    {
        "email": "string (email)",
        "password": "string (min: 8)"
    }

    ```
  * Response:
    ```json
    {
        "access_token": "string",
        "token_type": "string"
    }

    ```
  * Status Codes:
    * 200 OK
    * 401 Unauthorized (Invalid credentials)
    * 422 Unprocessable Content (Validation errors)
    * 429 Too Many Requests (Rate Limiting)

#### 5.2 Expenses

* **`GET /expenses`**
  * Description: Get all expenses for the user's company.
  * Roles: Any
  * Request:
    * Headers: `Authorization: Bearer {token}`
    * Query Parameters:
      * `page`: integer (optional, for pagination)
      * `search`: string (optional, search by title or category)
  * Response:
    ```json
    {
        "data": [
            {
                "id": "integer",
                "company_id": "integer",
                "user_id": "integer",
                "title": "string",
                "amount": "decimal",
                "category": "string",
                "created_at": "datetime",
                "updated_at": "datetime",
                "user": { // Eager loaded
                    "id": "integer",
                    "name": "string"
                }
            },
            // ...
        ],
        "links": { // Pagination links
            "first": "string",
            "last": "string",
            "prev": "string",
            "next": "string"
        },
        "meta"?: { // Pagination meta
            "current_page": "integer",
            "from": "integer",
            "last_page": "integer",
            "path": "string",
            "per_page": "integer",
            "to": "integer",
            "total": "integer"
        }
    }

    ```
  * Status Codes:
    * 200 OK
    * 401 Unauthorized
* **`POST /expenses`**
  * Description: Create a new expense for the user's company.
  * Roles: Any
  * Request:

    * Headers: `Authorization: Bearer {token}`

    ```json
    {
        "title": "string (required, max: 255)",
        "amount": "decimal (required, min: 0.01)",
        "category": "string (required, max: 255)"
    }

    ```
  * Response:

    ```json
    {
        "id": "integer",
        "company_id": "integer",
        "user_id": "integer",
        "title": "string",
        "amount": "decimal",
        "category": "string",
        "created_at": "datetime",
        "updated_at": "datetime"
    }

    ```
  * Status Codes:

    * 201 Created
    * 401 Unauthorized
    * 422 Unprocessable Content (Validation errors)
* **`PUT /expenses/{expense}`**
  * Description: Update an existing expense.
  * Roles: Manager, Admin
  * Request:

    * Headers: `Authorization: Bearer {token}`

    ```json
    {
        "title": "string (max: 255)",
        "amount": "decimal (min: 0.01)",
        "category": "string (max: 255)"
    }

    ```
  * Response:

    ```json
    {
        "id": "integer",
        "company_id": "integer",
        "user_id": "integer",
        "title": "string",
        "amount": "decimal",
        "category": "string",
        "created_at": "datetime",
        "updated_at": "datetime"
    }

    ```
  * Status Codes:

    * 200 OK
    * 401 Unauthorized
    * 403 Forbidden (Unauthorized - non-Manager/Admin, or different company)
    * 422 Unprocessable Content (Validation errors)
* **`DELETE /expenses/{expense}`**
  * Description: Delete an expense.
  * Roles: Admin
  * Request:
    * Headers: `Authorization: Bearer {token}`
  * Response:
    ```json
    {
        "message": "Expense deleted successfully"
    }

    ```
  * Status Codes:
    * 200 OK
    * 401 Unauthorized
    * 403 Forbidden (Unauthorized - non-Admin, or different company)

#### 5.3 Users

* **`GET /users`**
  * Description: Get all users for the admin's company.
  * Roles: Admin
  * Request:
    * Headers: `Authorization: Bearer {token}`
  * Response:
    ```json
    {
        "data": [
            {
                "id": "integer",
                "company_id": "integer",
                "name": "string",
                "email": "string",
                "role": "string",
                "created_at": "datetime",
                "updated_at": "datetime"
            },
            // ...
        ],
         "links": { // Pagination links
            "first": "string",
            "last": "string",
            "prev": "string",
            "next": "string"
        },
        "meta": { // Pagination meta
            "current_page": "integer",
            "from": "integer",
            "last_page": "integer",
            "path": "string",
            "per_page": "integer",
            "to": "integer",
            "total": "integer"
        }
    }

    ```
  * Status Codes:
    * 200 OK
    * 401 Unauthorized
    * 403 Forbidden (Unauthorized - non-Admin)
* **`POST /users`**
  * Description: Add a new user to the admin's company.
  * Roles: Admin
  * Request:

    * Headers: `Authorization: Bearer {token}`

    ```json
    {
        "name": "string",
        "email": "string (email, unique)",
        "password": "string (min: 8, confirmed)",
        "role": "string (enum: Administrator, Manager, Employee)"
    }

    ```
  * Response:

    ```json
    {
        "id": "integer",
        "company_id": "integer",
        "name": "string",
        "email": "string",
        "role": "string",
        "created_at": "datetime",
        "updated_at": "datetime"
    }

    ```
  * Status Codes:

    * 201 Created
    * 401 Unauthorized
    * 403 Forbidden (Unauthorized - non-Admin)
    * 422 Unprocessable Content (Validation errors)
* **`PUT /users/{user}`**
  * Description: Update a user's role.
  * Roles: Admin
  * Request:

    * Headers: `Authorization: Bearer {token}`

    ```json
    {
        "role": "string (enum: Administrator, Manager, Employee)"
    }

    ```
  * Response:

    ```json
    {
        "id": "integer",
        "company_id": "integer",
        "name": "string",
        "email": "string",
        "role": "string",
        "created_at": "datetime",
        "updated_at": "datetime"
    }

    ```
  * Status Codes:

    * 200 OK
    * 401 Unauthorized
    * 403 Forbidden (Unauthorized - non-Admin, or different company)
    * 422 Unprocessable Content (Validation errors)

### 6. Audit Logs

Every update and delete action on expenses is logged in the `audit_logs` table. The log includes:

* `user_id`: The ID of the user who performed the action.
* `company_id`: The ID of the company the action was performed in.
* `action`: The type of action ('expense_updated', 'expense_deleted').
* `changes`: A JSON object containing the old and new values for updates.

### 7. Rate Limiting

The following endpoints are rate limited:

* `POST /register`: 5 attempts per 60 minutes.
* `POST /login`: 10 attempts per 60 minutes.

### 8. Caching

The `GET /expenses` endpoint is cached using Redis. The cache is invalidated when an expense is created, updated, or deleted.

### 9. Background Jobs

A weekly expense report is generated and sent to all admins every Monday at 8:00 AM. The report includes a summary of expenses for the previous week, broken down by company.

### 10. Testing

The application is thoroughly tested using Pest, covering:

* ✅ **Authentication:** Register, login, and access restrictions
* 🔐 **RBAC:** Ensures Admins, Managers, and Employees only access permitted actions
* 💰 **Expenses:** CRUD operations scoped by company and role
* 👥 **Users:** Admin-only user management (add, list, update, delete)
* 🧾 **Audit Logs:** Logs all updates and deletions to expenses
* ✉️ **Background Jobs:** Weekly report mail job verified
* ⚙️ **Security:** All tests ensure multi-tenancy and role isolation are enforced

Tests ensure correctness, security, and performance of core business logic.

### 11. Bonus: Web-page as a Test UI

The application includes a single [html page](test-client-page.html) ([test-client-page.html](test-client-page.html)) to use or test the app's features. (Works perfectly but not Ideal for production).
It can be used in the app or as an external client.
The page is made with HTML, JavaScript and Bootstrap

---

<h2 align="center">Made With </h2>
<p align="center"> <a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel"></a></p>

<!-- <p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p> -->
