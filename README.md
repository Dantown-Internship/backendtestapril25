## 📘 Project Summary

This project is a **Multi-Tenant SaaS-Based Expense Management API** built with Laravel 10. It allows multiple companies to securely manage their expenses with complete data isolation. The system supports user roles such as **Admin**, **Manager**, and **Employee**, each with defined access levels.

Key features include:

-   **Secure API authentication** using Laravel Sanctum.
-   **Role-Based Access Control (RBAC)** with scoped permissions.
-   **Multi-tenancy** enforced via relationships and global scopes.
-   **Expense management** (create, view, update, delete) with role-specific restrictions.
-   **Weekly expense report dispatching** using Laravel Queues and Bus Batching.
-   **Audit logging** for all update and delete actions on expenses.
-   **UUIDs** used for public-facing identifiers to enhance security.
-   **Redis caching** applied selectively for optimal performance.

The API adheres to Laravel best practices, ensuring performance, security, and maintainability in a SaaS environment.

## 🛠️ Tech Stack

The project is built using the following technologies:

-   **PHP 8.2+** – Primary backend language
-   **Laravel 10** – Backend framework
-   **MySQL** – Relational database
-   **Laravel Sanctum** – Token-based API authentication
-   **Redis | Database** – Caching and queue management
-   **Laravel Queues** – For handling background jobs
-   **Laravel Scheduler** – For scheduling recurring tasks
-   **UUIDs** – Used as public-facing identifiers
-   **Pest PHP** – For automated testing

## Postman API Documentation

For detailed API documentation, including example requests and responses, please refer to the Postman collection:

[View Postman Documentation](https://documenter.getpostman.com/view/25344834/2sB2cd4dL2)

This link will guide you through the API's functionality and provide you with all the necessary information for integrating with our API.

### Features:

-   Easy access to all endpoints
-   Example/Sample requests and responses
-   Authentication details
-   Sample data for testing

## Project Setup

To set up the project on your local environment, follow these steps:

### 1. Clone the repository

Clone the repository to your local machine:

```bash
git clone https://github.com/FavourOladeji/backendtestapril25.git
cd backendtestapril25
git checkout favour-oladeji
```

### 2. Install Dependencies

Run `composer install` to install the project dependencies:

```bash
composer install
```

### 3. Set up Environment Variables

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

### 4. Set up Database

-   Open the `.env` file and configure the following database settings according to your local or production environment:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password
    ```

### 5. Add Mailing Credentials

Open the `.env` file and add your mailing credentials. Look for the following configuration keys:

```bash
MAIL_MAILER=smtp
MAIL_HOST=your-mail-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Replace the placeholder values with your actual email credentials. You can get these details from your email provider (e.g., Gmail, SendGrid, etc.).

### 6. Generate Application Key

Generate the application key:

```bash
php artisan key:generate
```

### 7. Run Migrations and Seed the Database

Run the migrations and seed the database with sample data:

```bash
php artisan migrate --seed
```

### 8. Seeded Credentials

The following user credentials have been seeded for testing:

-   **Admin User**

    -   Email: `admin1@mail.com`
    -   Password: `password`

-   **Employee User**

    -   Email: `employee1@mail.com`
    -   Password: `password`

-   **Manager User**
    -   Email: `manager1@mail.com`
    -   Password: `password`

### 9. Seeded Companies and Users

The database has been seeded with the following data:

-   **5 companies** have been seeded, each with **3 users**.
-   Each user has been assigned **5 expenses**.

You can now log in using the seeded credentials and start testing the application.

### 10. Running the Queue Worker

To run the queue worker, execute the following command:

```bash
php artisan queue:work
```

### 11. Running the Scheduler

To run the scheduler, execute the following command:

```bash
php artisan schedule:run
```

### 12. Dispatch Expense Report Immediately

To dispatch the expense report immediately, execute the following command:

```bash
php artisan expense:report
```

---

That's it! You are now ready to use the application.

## 🧩 Additional Notes, Considerations & Additions

### **Multitenancy via Global Scope**

A `CompanyScope` global scope is applied to all tenant-aware models to ensure tenant isolation. This guarantees:

-   Every query is automatically scoped to the authenticated user's company.
-   Data belonging to other tenants is not exposed or accessed by mistake.

This design enforces strict tenant isolation and prevents data leaks.

### **Authorization via Policies**

Laravel’s **Policy classes** are used to authorize expense-related actions (view, update, delete) based on user roles and company ownership. This ensures that users can only interact with resources within their permission scope.

### **Role Middleware & Helper for Clean Access Control**

A custom `RoleMiddleware` class restricts access to API routes based on user roles (e.g., Admin, Manager, Employee).  
The `roleMiddleware()` helper function generates middleware **aliases** and corresponding **role strings** dynamically, ensuring consistency and eliminating hard-coded role references in route definitions.

### **API Versioning**

To ensure backward compatibility and smooth transitions, API versioning has been introduced. You can now specify the version of the API when making requests.  
Example:  
`GET 127.0.0.1:8000/v1/users`  
This approach allows new features and improvements without breaking existing integrations.

### **Expense Persistence on User Deletion**

Expenses are preserved even if a user is deleted. The `user_id` on the `expenses` table is nullable and uses `nullOnDelete()`, ensuring historical expense data remains intact.

### **Scoped Expense Listing**

-   **Employees**: Can only view their own expenses.
-   **Managers** and **Admins**: Can view all expenses within their company, including who created each expense.

### **Standardized API Responses Using a Trait**

API responses follow a consistent format through a custom `HasApiResponse` trait. This trait ensures success, error, and paginated responses have a unified structure, simplifying response handling for frontend consumers.

### **UUIDs for Public-Facing Identifiers**

UUIDs are used for public-facing identifiers in API responses and route parameters, avoiding exposure of internal auto-incrementing IDs. Internally, primary `id` fields are still used for efficiency.  
The `HasUUID` trait:

-   Automatically assigns an **ordered UUID** during model creation.
-   Enables **route model binding** using the UUID instead of numeric IDs, improving security and consistency.

### **Caching Strategy**

A dedicated `CacheKey` helper class ensures consistent cache key naming, reducing the risk of cache collisions. Caching is selectively applied as follows:

-   Dynamic data (like paginated records) **is not cached** due to frequent changes.
-   The list of **company admins** is cached and invalidated when admins are added, removed, or their roles change.
-   For paginated data caching, **full request URLs** (including query parameters) are used as cache keys.

### **Weekly Expense Report**

The **Weekly Expense Report** summarizes weekly expenses, showing:

-   **Total Amount**: Displays the total expenses incurred.
-   **Expense Breakdown by Categories**: Provides a detailed breakdown of expenses by category.
-   **Top 5 Spenders**: Lists the top 5 users with the highest expenses for the week.

![alt text](https://github.com/FavourOladeji/backendtestapril25/blob/favour-oladeji/public/report.png?raw=true)

### **Weekly Expense Report via Batching**

A custom Artisan command dispatches weekly expense report jobs using `Bus::batch()`. Each job aggregates the past week’s expenses and notifies all company admins with the report. This scalable design ensures fault-tolerant processing of reports across multiple tenants.

### **Audit Log Implementation**

An audit log system is in place to track all changes to sensitive models. To ensure proper type safety when creating audit logs, a custom cast was implemented that casts the changes JSON column to an `AuditLogChangesDto`. This ensures that all changes are typed and properly structured for easy retrieval and analysis.  
Admin users have access to the audit logs and can filter logs based on the **action** performed (update, delete) and the **time** the action occurred.

### **Indexing for Performance**

Since the application is multi-tenant, indexing was carefully implemented to optimize query performance. Composite indexes have been added to frequently filtered columns, ensuring efficient retrieval of data. There was no additional need to create an index on the `company_id` column as MYSQL automatically adds an index on foreign keys.  
Specifically, the following composite indexes were added:

-   `company_id` + `uuid` (for fast access to data by company and UUID)
-   `company_id` + `id` (for efficient access to primary key-based queries)

These indexes ensure that queries are executed efficiently, even when filtering large datasets across different tenants.

### **Code Formatting**

The project uses **Laravel Pint** for automatic code formatting. This ensures the codebase adheres to a consistent style, making it easier to maintain and collaborate on.

## 🧪 Testing

-   All API endpoints were thoroughly tested using feature tests.
-   Assertions were made to verify the **correct JSON response structure**, status codes, and expected data outputs for each scenario.
-   The **weekly expense report** command and its dispatched jobs were also tested to ensure:
    -   Reports are correctly generated per company.
    -   Admins receive the appropriate notifications.
-   Writing the tests early made it easier to **refactor and improve the codebase** with confidence, ensuring changes didn’t break existing functionality

### Running Tests

To run the tests, ensure the **SQLite extension** is enabled for your PHP setup. Alternatively, you can modify the `phpunit.xml` file to use your preferred database connection. Once set up, you can execute the tests by running the following Artisan command:

```bash
php artisan test

```
