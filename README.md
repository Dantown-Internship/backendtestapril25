# 💾 Mid-Level Technical Test – Multi-Tenant SaaS-Based Expense Management API by [Bowofade Oyerinde](https://bowofade.com)

Welcome to the Multi-Tenant SaaS Expense Management API built with **Laravel 12**. This README will guide you through installing, configuring, and running the application, including database setup, caching, queue processing, and scheduled tasks.

I built this project as part of a technical interview to demonstrate my understanding of scalable architecture, multi-tenancy, and clean Laravel practices.

---

## 🔧 Prerequisites

-   **PHP 8.2+**
-   **Composer**
-   **MySQL 5.7+**
-   **Redis** (optional, recommended for caching & queue)
-   **SMTP** credentials for email notifications
-   **Git**

> **Note:** If you don't have Redis installed, you may use any supported cache/queue driver (e.g., `file`, `database`).

---

## 📅 Installation

1. **Clone the repository**

    ```bash
    https://github.com/Dantown-Internship/backendtestapril25.git Dantown
    cd Dantown
    ```

2. **Install dependencies**

    ```bash
    composer install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configure `.env`**

    ```dotenv
    APP_ENV=local
    APP_DEBUG=true
    APP_URL=http://localhost:8000

    # Database
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=expense_db
    DB_USERNAME=root
    DB_PASSWORD=

    # Cache & Queue
    CACHE_DRIVER=redis        # or file, database, etc.
    QUEUE_CONNECTION=redis    # or database

    # Redis (if used)
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    # Mail
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_username
    MAIL_PASSWORD=your_password
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=hello@example.com
    MAIL_FROM_NAME="Expense API"
    ```

---

## 🗂️ Database Setup (Migration & Seeder)

1. **Create the database**

    ```sql
    CREATE DATABASE expense_db;
    ```

2. **Run migration and seeders immediately**

    ```bash
    php artisan migrate --seed
    ```

    This step will:

    - Migrate the tables
    - Seed **two companies** and their respective **admin users**:
        - Email: `admin@company1.com`, `admin@company2.com`
        - Password: `password`

    > Be sure to replace these emails with functional ones in `DatabaseSeeder.php` to receive the scheduled report emails.

---

## ⚙️ Caching

-   I used Redis to cache frequently accessed data for performance.
-   Cache is **automatically invalidated** on create, update, and delete.
-   You can configure the cache driver in `.env`:
    ```dotenv
    CACHE_DRIVER=file
    ```

---

## 🧵 Queue & Scheduler

-   **Queue Driver**: Controlled via `QUEUE_CONNECTION`.
-   Run the queue worker:

    ```bash
    php artisan queue:work
    ```

-   **Scheduled Tasks**:
    -   I configured a Laravel `schedule:run` job to send **weekly expense reports** to company admins.

### 🔄 Schedule Setup & Implementation Details

To run the weekly expense report scheduler, add this to your server's crontab:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

> You can adjust the schedule to run every minute for testing purposes inside `App\Console\Kernel.php`.

The schedule is defined in `console.php`.

---

## 🔐 Authentication & RBAC

-   Used **Laravel Sanctum** for API token authentication.
-   Implemented **Role-Based Access Control (RBAC)** via policies:

    -   **Admin**: Full access to users and expenses
    -   **Manager**: Manage expenses
    -   **Employee**: View and create expenses

-   I used **Global Scopes and Traits** to:
    -   Filter all fetched data based on both the authenticated user **and their company ID**.
    -   Automatically attach the authenticated user's `company_id` to any resource being created.

---

## 📜 API Versioning & Routes

All routes are versioned with the prefix `/api/v1`.
I created a dedicated `routes/v1` file to organize versioned APIs cleanly, ensuring maintainability and scalability for future versions like `v2`.

---

## 📜 API Endpoints

### Authentication

| Method | URI           | Description       |
| ------ | ------------- | ----------------- |
| POST   | /api/v1/login | Authenticate user |

> 🛡️ **Why No Register Endpoint?**
>
> The register endpoint was intentionally omitted for security reasons:
>
> **Security First Approach:**
>
> -   In a multi-tenant SaaS application, allowing public registration of admin accounts creates security vulnerabilities
> -   Anyone could register as an admin and create a company
>
> **Proper Implementation Should Have:**
>
> -   A super-admin endpoint
> -   Controlled admin account creation
>
> **Current Implementation:**
>
> -   Uses database seeding to create initial companies and admin accounts
> -   Admins can then create other users within their company
> -   This ensures controlled access and proper data isolation

### Test Credentials

-   Email: `admin@company1.com`
-   Email: `admin@company2.com`
-   Password: `password`

### Expenses

| Method | URI                   | Role            | Description                           |
| ------ | --------------------- | --------------- | ------------------------------------- |
| GET    | /api/v1/expenses      | Authenticated   | List expenses (paginated, searchable) |
| POST   | /api/v1/expenses      | Employee+       | Create expense                        |
| PUT    | /api/v1/expenses/{id} | Manager+, Admin | Update expense                        |
| DELETE | /api/v1/expenses/{id} | Admin           | Delete expense                        |

### Users

| Method | URI                | Role  | Description      |
| ------ | ------------------ | ----- | ---------------- |
| GET    | /api/v1/users      | Admin | List users       |
| POST   | /api/v1/users      | Admin | Add new user     |
| PUT    | /api/v1/users/{id} | Admin | Update user role |

---

## 📊 Response Format

All responses follow a standardized structure using a Trait I created:

```json
// Success
{
  "success": true,
  "data": { /* response data */ },
  "message": "Operation successful."
}

// Error
{
  "success": false,
  "message": "Unauthorized access.",
  "errors": { /* optional validation errors */ }
}
```

Proper HTTP status codes are returned, such as `200`, `201`, `403`, `422`, etc.

---

## 🧾 Audit Logs

-   Logs every update/delete action on expenses.
-   Captures `user_id`, `company_id`, the action, and before/after change details.

---

## 🚀 Running the App

```bash
php artisan serve
```

-   Visit: [http://localhost:8000](http://localhost:8000)
-   Use seeded credentials to log in.
-   Make sure queue worker and schedule runner are active to see full functionality.

---

## 🆘 Troubleshooting

-   **Redis not installed**: Use `file` or `database` drivers and run:

    ```bash
    php artisan config:clear && php artisan cache:clear
    ```

-   **Email not sent**: Check your SMTP settings and try with Mailtrap or another test service.

-   **403 errors**: Confirm that your user has the correct role.

-   **No scheduled emails**: Ensure cron is running and email addresses are valid.

---

Thank you for reviewing my project. I built this with best practices in mind and would love to discuss any part of the implementation!
