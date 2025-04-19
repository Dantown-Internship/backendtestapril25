##  API Documentation

###  Authentication

#### POST `/api/register`

Registers a new user.

- **Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password",
  "company_name": "Acme Inc"
}
```

- **Response:**
```json
{
  "token": "your_access_token",
  "user": { ... }
}
```

---

#### POST `/api/login`

Logs in an existing user.

- **Body:**
```json
{
  "email": "john@example.com",
  "password": "password"
}
```

- **Response:**
```json
{
  "token": "your_access_token",
  "user": { ... }
}
```

---

###  User Management (Admin Only)

#### GET `/api/users`

Returns all users within the admin's company.

- **Auth Required:** Bearer Token (Admin)

---

#### POST `/api/users`

Creates a new user in the same company.

- **Body:**
```json
{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "password": "password",
  "role": "User"
}
```

---

#### PUT `/api/users/{id}`

Updates a user’s details.

- **Body:**
```json
{
  "name": "Updated Name",
  "role": "User"
}
```

---

###  Expense Management

#### GET `/api/expenses`

Returns a paginated list of expenses with optional filters.

- **Query Params (optional):**
  - `category=Travel`
  - `date_from=2024-01-01`
  - `date_to=2024-02-01`

- **Auth Required:** Bearer Token

---

#### POST `/api/expenses`

Creates a new expense.

- **Body:**
```json
{
  "title": "Flight to Berlin",
  "amount": 500.00,
  "category": "Travel",
  "incurred_on": "2025-04-01"
}
```

---

#### PUT `/api/expenses/{id}`

Updates an existing expense.

---

#### DELETE `/api/expenses/{id}`

Deletes an expense. Automatically logs the action in `audit_logs`.

---

###  Audit Logs

- No public endpoints.
- Logs are stored automatically during create/update/delete actions:
  - `user_id`
  - `company_id`
  - `action` (`created`, `updated`, `deleted`)
  - `table` and `record_id`
  - `changes` (for update)

