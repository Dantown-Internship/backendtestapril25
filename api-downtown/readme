# 🧾 Mid-Level Technical Test – Multi-Tenant SaaS-Based Expense Management API

Welcome! This is a technical test for laravel backend developers.

Your task is to build a secure, high-performance API for a **Multi-Tenant SaaS-based Expense Management System**, where multiple companies can manage their expenses independently. Please follow the instructions below and submit your solution as described.

---

## 🚀 Project Requirements

### ✅ Key Features to Implement

- **Multi-Tenant Support** – Companies should have isolated data.
- **Secure API Authentication** – Use Laravel Sanctum.
- **Role-Based Access Control (RBAC)** – Admins, Managers, Employees.
- **Advanced Query Optimization** – Indexing, Eager Loading.
- **Background Job Processing** – Laravel Queues.
- **Audit Logging** – Track changes to expenses.

---

## 🗂️ Tasks Breakdown

### 🏗️ Task 1: Multi-Tenant Database Structure (Migrations & Models)

#### Companies Table
- Fields: `id`, `name`, `email`, `created_at`, `updated_at`

#### Users Table (Modified)
- Add `company_id` (Foreign Key)
- Add `role` (Enum: `["Admin", "Manager", "Employee"]`)

#### Expenses Table
- Fields: `id`, `company_id`, `user_id`, `title`, `amount`, `category`, `created_at`, `updated_at`
- Add an index on `company_id` for performance

#### Relationships
- A **Company** has many **Users**
- A **User** belongs to a **Company**
- A **User** has many **Expenses**

---

### 🔐 Task 2: API Authentication & RBAC

- Use **Laravel Sanctum** for token-based authentication
- Implement Role-Based Access Control:
  - **Admin**: Manage users & expenses
  - **Manager**: Manage expenses (cannot delete users)
  - **Employee**: View and create expenses
- Ensure users **cannot access data** from other companies

---

### 🧾 Task 3: API Endpoints

#### Authentication
- `POST /api/register` → Admin only
- `POST /api/login`

#### Expense Management
- `GET /api/expenses` → List (by company, paginated, searchable by title/category)
- `POST /api/expenses` → Create (restricted to logged-in user’s company)
- `PUT /api/expenses/{id}` → Update (Managers & Admins only)
- `DELETE /api/expenses/{id}` → Delete (Admins only)

#### User Management
- `GET /api/users` → List users (Admins only)
- `POST /api/users` → Add user (Admins only)
- `PUT /api/users/{id}` → Update user role (Admins only)

---

### ⚙️ Task 4: Optimization & Performance

- Use **Eager Loading** (`with()`) to avoid N+1 queries
- Add **indexes** on `company_id` and `user_id` in the expenses table
- Implement **Redis caching** for frequently accessed queries

---

### 🧵 Task 5: Background Job Processing

- Use Laravel Queues (with `database` or `redis` driver)
- Create a **weekly job** that sends an expense report to all Admins
- Use Laravel’s **scheduler** (`schedule:run`) to run the job

---

### 🕵️‍♀️ Task 6: Audit Logs

#### Audit Logs Table
- Fields: `id`, `user_id`, `company_id`, `action`, `changes`, `created_at`

#### Requirements
- Log every **update/delete** action on expenses
- Store the **old and new values** of each expense before update

---

## 🛠️ Tech Stack

- Laravel 10+
- MySQL or PostgreSQL
- Laravel Sanctum
- Redis (optional but recommended)
- Laravel Queues & Scheduler

---

## 📬 How to Submit

1. **Fork** this repository.
2. **Clone** the forked repository to your local machine.
3. Create a new **branch** using your full name (e.g., `john-doe`):

   ```bash
   git checkout -b john-doe
4. Complete the tasks outlined above.
5. Push your branch to your forked repository:

   git push origin your-branch-name

6. Create a Pull Request (PR) to the original repository’s `main` branch.

7. In the PR description, please include:
   - Your full name
   - Any notes or assumptions made
   - Features you implemented or skipped (with reasons)
   - Any instructions for testing (if applicable)

---

## ✅ Evaluation Criteria

- Correctness & completeness of features  
- Code structure and readability  
- Proper use of Laravel best practices  
- Security and role enforcement  
- Performance optimizations  
- Bonus: Tests, Redis integration, and proper API responses  

---

Good luck! 🍀 Feel free to reach out if you need clarification on any part of the task.


