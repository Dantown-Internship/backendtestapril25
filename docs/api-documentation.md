# Multi-Tenant SaaS-Based Expense Management API

This document provides a comprehensive guide to the Expense Management API, designed for multi-tenant SaaS environments.

## Authentication

### Register a New Company and Admin User

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
  "company_name": "Your Company Name",
  "company_email": "company@example.com",
  "name": "Admin Name",
  "email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "message": "Company and admin user registered successfully",
  "user": {
    "id": 1,
    "name": "Admin Name",
    "email": "admin@example.com",
    "company_id": 1,
    "role": "Admin"
  },
  "company": {
    "id": 1,
    "name": "Your Company Name",
    "email": "company@example.com"
  },
  "token": "your_api_token"
}
```

### Login

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin Name",
    "email": "admin@example.com",
    "company_id": 1,
    "role": "Admin"
  },
  "company": {
    "id": 1,
    "name": "Your Company Name",
    "email": "company@example.com"
  },
  "token": "your_api_token"
}
```

### Logout

**Endpoint:** `POST /api/logout`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

### Get User Profile

**Endpoint:** `GET /api/user`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Admin Name",
    "email": "admin@example.com",
    "company_id": 1,
    "role": "Admin",
    "company": {
      "id": 1,
      "name": "Your Company Name",
      "email": "company@example.com"
    }
  }
}
```

## Company Management

### Get Company Details

**Endpoint:** `GET /api/company`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "company": {
    "id": 1,
    "name": "Your Company Name",
    "email": "company@example.com",
    "created_at": "2025-04-14T12:00:00.000000Z",
    "updated_at": "2025-04-14T12:00:00.000000Z"
  }
}
```

### Update Company

**Endpoint:** `PUT /api/company`

**Headers:**
- Authorization: Bearer your_api_token

**Request Body:**
```json
{
  "name": "Updated Company Name",
  "email": "updated@example.com"
}
```

**Response:**
```json
{
  "message": "Company updated successfully",
  "company": {
    "id": 1,
    "name": "Updated Company Name",
    "email": "updated@example.com",
    "created_at": "2025-04-14T12:00:00.000000Z",
    "updated_at": "2025-04-14T12:30:00.000000Z"
  }
}
```

### Get Company Statistics

**Endpoint:** `GET /api/company/statistics`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "total_expenses": 12500.75,
  "user_count": 12,
  "expense_count": 45,
  "recent_expenses": [
    {
      "id": 45,
      "title": "Business trip to LA",
      "amount": 1250.00,
      "category": "Travel",
      "user": {
        "id": 5,
        "name": "John Doe"
      }
    }
  ],
  "expenses_by_category": [
    {
      "category": "Travel",
      "total": 5678.90
    },
    {
      "category": "Meals",
      "total": 1234.56
    }
  ]
}
```

## User Management

### List Company Users

**Endpoint:** `GET /api/users`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "Admin",
      "company_id": 1
    }
  ],
  "total": 10,
  "per_page": 10
}
```

### Create User

**Endpoint:** `POST /api/users`

**Headers:**
- Authorization: Bearer your_api_token

**Request Body:**
```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123",
  "role": "Manager"
}
```

**Response:**
```json
{
  "message": "User created successfully",
  "user": {
    "id": 10,
    "name": "New User",
    "email": "newuser@example.com",
    "role": "Manager",
    "company_id": 1
  }
}
```

### Get User Details

**Endpoint:** `GET /api/users/{id}`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "user": {
    "id": 10,
    "name": "New User",
    "email": "newuser@example.com",
    "role": "Manager",
    "company_id": 1
  }
}
```

### Update User

**Endpoint:** `PUT /api/users/{id}`

**Headers:**
- Authorization: Bearer your_api_token

**Request Body:**
```json
{
  "name": "Updated User Name",
  "email": "updated_user@example.com",
  "role": "Employee"
}
```

**Response:**
```json
{
  "message": "User updated successfully",
  "user": {
    "id": 10,
    "name": "Updated User Name",
    "email": "updated_user@example.com",
    "role": "Employee",
    "company_id": 1
  }
}
```

### Delete User

**Endpoint:** `DELETE /api/users/{id}`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "message": "User deleted successfully"
}
```

## Expense Management

### List Expenses

**Endpoint:** `GET /api/expenses`

**Headers:**
- Authorization: Bearer your_api_token

**Query Parameters (all optional):**
- `search`: Search term for expense title
- `category`: Filter by expense category
- `start_date`: Filter expenses after this date (YYYY-MM-DD)
- `end_date`: Filter expenses before this date (YYYY-MM-DD)
- `min_amount`: Filter expenses with amount greater than or equal to this value
- `max_amount`: Filter expenses with amount less than or equal to this value
- `sort_by`: Field to sort by (default: 'created_at')
- `sort_direction`: 'asc' or 'desc' (default: 'desc')

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Office supplies",
      "amount": "150.75",
      "category": "Office Supplies",
      "company_id": 1,
      "user_id": 10,
      "created_at": "2025-04-14T12:30:00.000000Z",
      "updated_at": "2025-04-14T12:30:00.000000Z",
      "user": {
        "id": 10,
        "name": "John Doe"
      }
    }
  ],
  "total": 45,
  "per_page": 10
}
```

### Create Expense

**Endpoint:** `POST /api/expenses`

**Headers:**
- Authorization: Bearer your_api_token

**Request Body:**
```json
{
  "title": "Business lunch with client",
  "amount": 78.50,
  "category": "Meals"
}
```

**Response:**
```json
{
  "message": "Expense created successfully",
  "expense": {
    "id": 46,
    "title": "Business lunch with client",
    "amount": "78.50",
    "category": "Meals",
    "company_id": 1,
    "user_id": 10,
    "created_at": "2025-04-14T14:15:00.000000Z",
    "updated_at": "2025-04-14T14:15:00.000000Z",
    "user": {
      "id": 10,
      "name": "John Doe"
    }
  }
}
```

### Get Expense Details

**Endpoint:** `GET /api/expenses/{id}`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "expense": {
    "id": 46,
    "title": "Business lunch with client",
    "amount": "78.50",
    "category": "Meals",
    "company_id": 1,
    "user_id": 10,
    "created_at": "2025-04-14T14:15:00.000000Z",
    "updated_at": "2025-04-14T14:15:00.000000Z",
    "user": {
      "id": 10,
      "name": "John Doe"
    }
  }
}
```

### Update Expense

**Endpoint:** `PUT /api/expenses/{id}`

**Headers:**
- Authorization: Bearer your_api_token

**Request Body:**
```json
{
  "title": "Updated expense title",
  "amount": 85.75,
  "category": "Meals"
}
```

**Response:**
```json
{
  "message": "Expense updated successfully",
  "expense": {
    "id": 46,
    "title": "Updated expense title",
    "amount": "85.75",
    "category": "Meals",
    "company_id": 1,
    "user_id": 10,
    "created_at": "2025-04-14T14:15:00.000000Z",
    "updated_at": "2025-04-14T14:30:00.000000Z",
    "user": {
      "id": 10,
      "name": "John Doe"
    }
  }
}
```

### Delete Expense

**Endpoint:** `DELETE /api/expenses/{id}`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "message": "Expense deleted successfully"
}
```

### Get Expense Audit Logs

**Endpoint:** `GET /api/expenses/{id}/audit-logs`

**Headers:**
- Authorization: Bearer your_api_token

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 3,
      "user_id": 1,
      "company_id": 1,
      "action": "update",
      "changes": {
        "expense_id": 46,
        "old": {
          "id": 46,
          "title": "Business lunch with client",
          "amount": "78.50",
          "category": "Meals"
        },
        "new": {
          "id": 46,
          "title": "Updated expense title",
          "amount": "85.75",
          "category": "Meals"
        }
      },
      "created_at": "2025-04-14T14:30:00.000000Z",
      "updated_at": "2025-04-14T14:30:00.000000Z",
      "user": {
        "id": 1,
        "name": "Admin User"
      }
    }
  ],
  "total": 2,
  "per_page": 10
}
```

## Role-Based Access Control

The API implements a role-based access control system with the following roles:

### Admin
- Can manage company settings
- Can create, view, update, and delete any user in their company
- Can create, view, update, and delete any expense in their company
- Can view audit logs

### Manager
- Can view all users in their company
- Can create, view, update, and delete any expense in their company
- Can view audit logs

### Employee
- Can view their own profile
- Can only create, view, update, and delete their own expenses
- Cannot view audit logs

## Multi-Tenancy

The API enforces strict data isolation between companies. Users can only access data that belongs to their own company.