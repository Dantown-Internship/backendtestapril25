# 🧾 Expense Manager API

A secure, high-performance **Multi-Tenant SaaS-Based Expense Management System** built with Laravel 12.

---

## 🚀 Features

- ✅ **Multi-Tenant Architecture** – Company-scoped data isolation
- ✅ **Role-Based Access Control (RBAC)** – Admin, Manager, Employee
- ✅ **Authentication** – Laravel Sanctum
- ✅ **Expense CRUD** – With audit logs for update/delete
- ✅ **User Management** – Admin-only access
- ✅ **Optimization** – Redis caching, eager loading, indexed columns
- ✅ **Background Job** – Weekly email reports to company admins
- ✅ **API Response Format** – Consistent JSON responses

---

## 🛠️ Tech Stack

- Laravel 12.x
- Sanctum (API Auth)
- SQLite (can be switched to MySQL/PostgreSQL)
- Redis (optional)
- Laravel Queues (database driver)
- Scheduler & Jobs

---

## 📦 Setup Instructions

### 1. Clone the Repo

```bash
git clone <repo-url>
cd expense-manager
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Then:

```bash
touch database/database.sqlite
```

### 4. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed --class=CompanySeeder
```

### 5. Start the App

```bash
php artisan serve
```

---

## ✅ Running Queues & Scheduler

### Start Queue Worker

```bash
php artisan queue:work
```

### Trigger Scheduler (Weekly Report Job)

```bash
php artisan schedule:run
```

---

## 🔒 Authentication

- Register: `POST /api/register`
- Login: `POST /api/login` → returns `token`
- Authenticated routes require `Authorization: Bearer <token>`

---

## 📘 API Endpoints

### 💼 Expenses
- `GET /api/expenses`
- `POST /api/expenses`
- `PUT /api/expenses/<built-in function id>`
- `DELETE /api/expenses/<built-in function id>`

### 👤 Users (Admin Only)
- `GET /api/users`
- `POST /api/users`
- `PUT /api/users/<built-in function id>`

---

## 🗃️ Audit Logs

Stored in the `audit_logs` table:
- Tracks updates and deletions of expenses
- Captures before/after state

---

## 📬 Weekly Report Email

Sent to each company's Admin:
- Expense summary for the last 7 days
- Uses queued mail and Laravel scheduling

---

## 🧪 Testing via Postman/Curl

Ensure you use:

```
Accept: application/json
Authorization: Bearer <token>
```

---

## 📅 Created On

April 18, 2025

---

## 🧑‍💻 Author

Built by Samuel as part of a Laravel Backend Developer technical assessment.
