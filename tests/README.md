# 🧪 Feature Test Coverage for Expense Management API

This document summarizes the tests implemented using [Pest PHP](https://pestphp.com/) for the multi-tenant expense management backend API.

---

## ✅ Authentication Tests

### 1. Register (Admin Only)

* **Test:** `allows admin to register a user`
* **Description:** Ensures an admin user can successfully register a new user.
* **Expected Outcome:** HTTP 200, valid token response.

### 2. Register (Unauthorized)

* **Test:** `prevents non-admins from registering a user`
* **Description:** Ensures users with non-admin roles cannot register other users.
* **Expected Outcome:** HTTP 403 Forbidden

### 3. Login

* **Test:** `logs in with valid credentials`
* **Description:** Verifies login success for a user with correct credentials.
* **Expected Outcome:** HTTP 200, valid token.

### 4. Login (Invalid Password)

* **Test:** `fails login with incorrect credentials`
* **Description:** Verifies login fails with invalid credentials.
* **Expected Outcome:** HTTP 401 Unauthorized

---

## 🔐 Role-Based Access Control (RBAC) Tests

### 5. Employee cannot delete expense

* **Test:** `prevents employees from deleting expenses`
* **Description:** Confirms that users with role `Employee` cannot delete any expense.

### 6. Manager cannot manage users

* **Test:** `prevents managers from creating or updating users`
* **Description:** Ensures only Admins can perform user-related actions.

### 7. Employee can only create/view expenses

* **Test:** `allows employees to view and create expenses only`
* **Description:** Validates that employees cannot update or delete expenses.

---

## 💰 Expense Tests

### 8. List Expenses (Scoped by Company)

* **Test:** `shows expenses only for the user’s company`
* **Description:** Validates that users can only see expenses from their own company.

### 9. Create Expense

* **Test:** `creates an expense`
* **Description:** Ensures a user can create a new expense.

### 10. Update Expense

* **Test:** `updates an expense if user is Manager or Admin`
* **Description:** Only Manager/Admin can update expenses.

### 11. Delete Expense

* **Test:** `deletes an expense if user is Admin`
* **Description:** Only Admin can delete expenses.

---

## 👥 User Management Tests

### 12. List Users (Admin Only)

* **Test:** `lists all users in a company`
* **Description:** Admin can view a list of all users in their company.

### 13. Add User (Admin Only)

* **Test:** `adds a user to the company`
* **Description:** Admin can register a user within the same company.

### 14. Update User Role (Admin Only)

* **Test:** `updates a user’s role`
* **Description:** Admin can modify the role of an existing user.

---

## 🧾 Audit Log Tests

### 15. Log on Update

* **Test:** `logs an audit entry when an expense is updated`
* **Description:** Verifies that updates to expenses trigger audit logs capturing old and new values.

### 16. Log on Delete

* **Test:** `logs an audit entry when an expense is deleted`
* **Description:** Confirms a log entry is created when an expense is deleted (changes: `null`).

---

## ✉️ Mail Job Test

### 17. Weekly Report Job

* **Test:** (Manual)
* **Description:** A queued job runs weekly and sends expense reports to Admins. This was tested manually due to limitations in mail preview/testing in dev.

---

## 🧪 Test Tooling

* **Framework:** Pest PHP
* **Factories:** Used for the models (Company, User, Expense)
* **Sanctum Tokens:** Used to authenticate API requests
* **Refresh Database:** Used to ensure clean state for each test

---
