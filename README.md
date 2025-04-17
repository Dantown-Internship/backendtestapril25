


# Project Setup Guide



## Overview
 a **Multi-Tenant SaaS-based Expense Management System**, where multiple companies can manage their expenses independently.

## Prerequisites

Before you begin, make sure you have the following installed:


- **PHP** and **Composer**
- **MySQL** 

---

## Step-by-Step Setup

### 1. clone the **backend** repository


```bash
git clone https://github.com/Richswag009/backendtestapril25.git
cd backendtestapril25
```

### 3. Install Backend Dependencies

Install the required PHP dependencies using **Composer**:

```bash
composer install
```

This will download all necessary libraries for the backend.

### 3. Set Up the Database

1. **Create the Database**: Create a MySQL database 
2. **Configure `.env`**: Copy the `.env.example` file to `.env` and configure the database and other environment variables accordingly.

For MySQL, set the database connection:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```


### 4. Run Migrations

Run the database migrations to set up the necessary tables:

```bash
php artisan migrate
```

---

### 5. Generate Application Key

Run the following Artisan command to generate a unique application key. This key is used to secure user sessions and other encrypted data:

```bash
php artisan key:generate
```
This will update the .env file with a APP_KEY value.

---


### 7. Start the Application

Once everything is set up, you can test the application by visiting the url

```bash
php artisan serve
```
By default, the application will be accessible at http://localhost:8000/api.

---

# API Documentation for expense Management System

## Overview

This API provides endpoints to interact with the**Multi-Tenant SaaS-based Expense Management System**. It includes operations for registering users,login, managing expenses, including CRUD (Create, Read, Update, Delete) operations, pagination, and searching expenses based on category/title.

### Base URL

**http://localhost:8000/api**



### Register an admin n company

**POST** `/api/register`

#### Request

```json
  
   "full_name":"riches Metelewawon",
    "company_name":"riches andsons limited",
     "email" : "riches@gmail.com",
     "password":"riches"
```
---

### Response

```json

{
    "status": true,
    "message": "user register successfully",
   
}
```

---
## Authentication

### Sanctums Token Authentication

All endpoints require authentication via sanctum tokens. To authenticate, include the sanctum token in the `Authorization` header of your requests:

## Authorization: Bearer {sanctum_TOKEN}

### Obtaining the sanctum Token

To obtain a sanctum token, send a `POST` request to the `api/login` endpoint with your login credentials (email and password).

**POST** `pi/login`

#### Request

```json
  "email": "user@example.com",
  "password": "yourpassword"
```
---

### Response

```json

 {
    "status": true,
    "message": "login successful",
    "data": {
       
    }
}
```


---

Once you have the sanctum token, include it in the Authorization header for all further requests.

## Endpoints

### 1 Add new User
**POST /api/users** 

add new user of role employee or manager

### Example Request

```sql
POST /api/users
```
---
### Example Response
```json
   "name":"riches Metelewawon junior",
     "email" : "riches32@gmail.com",
     "password":"riches",
     "role":"Manager",
     "company_id":1
```
---
### Example Response
```json
{
    "status": true,
    "message": "User created successfully.",
    "data": {
        "name": "riches Metelewawon junior",
        "email": "riches02@gmail.com",
        "company_id": 1,
        "updated_at": "2025-04-17T21:54:40.000000Z",
        "created_at": "2025-04-17T21:54:40.000000Z",
        "id": 5,
        "role": "Manager"
    }
}
        
```
---


### 2. List All expenses
**GET /api/expenses** 



Fetches a list of all expenses for the authenticated user.

Query Parameters
- search: (optional) Filter expenses by category/title.
- per_page: (optional) Filter expenses per pages.
.

### Example Request

```sql
GET /api/expenses?priority=high&per_page=6&search=title
```
---
### Example Response
```json
  "data": [
            {
                "id": 6,
                "company_id": 1,
                "user_id": 1,
                "title": "Electronics",
                "amount": "200.00",
                "category": "fan",
                "created_at": "2025-04-17T20:46:29.000000Z",
                "updated_at": "2025-04-17T20:46:29.000000Z",
                "user": {
                    "id": 1,
                    "name": "riches Metelewawon",
                    "email": "riches2cute@gmail.com",
                    "role": "Admin",
                    "email_verified_at": null,
                    "created_at": "2025-04-14T12:36:01.000000Z",
                    "updated_at": "2025-04-14T12:36:01.000000Z",
                    "company_id": 1
                }
            },
       
        ],
```
---

### 2. Get Expense Details
**GET /api/expenses/{expense}**

Fetches details of a specific expense by its ID.

URL Parameters
- expense: The ID of the expense.

### Example Request

```sql
GET /api/expenses/1
```

### Example Response

```json
[
   {
                "id": 6,
                "company_id": 1,
                "user_id": 1,
                "title": "Electronics",
                "amount": "200.00",
                "category": "fan",
                "created_at": "2025-04-17T20:46:29.000000Z",
                "updated_at": "2025-04-17T20:46:29.000000Z",
                "user": {
                    "id": 1,
                    "name": "riches Metelewawon",
                    "email": "riches2cute@gmail.com",
                    "role": "Admin",
                    "email_verified_at": null,
                    "created_at": "2025-04-14T12:36:01.000000Z",
                    "updated_at": "2025-04-14T12:36:01.000000Z",
                    "company_id": 1
                }
            },
]
```
---


### 3. Create a New expense
**POST /api/expenses**

Create a new expense.

### Example Request
```json
{
 "title":"Electronics",
    "amount": "200",
    "category":" fan",

}
```

### Example Response

```json
"data": {
        "amount": "200",
        "title": "Electronics",
        "category": "fan",
        "company_id": "1",
        "user_id": 1,
        "updated_at": "2025-04-17T21:41:43.000000Z",
        "created_at": "2025-04-17T21:41:43.000000Z",
        "id": 8
    }
```
---
### 4. Update a expense

```sql
PUT /api/expenses/{expense}**
```

Update an existing expense by its ID.

URL Parameters
- expense: The ID of the expense.
### Example  Request
```json
{

 "title":"Electronics",
    "amount": "900",
    "category":" fan",
}
```
### Example Response
```json
"data": {
        "amount": "200",
        "title": "Electronics",
        "category": "fan",
        "company_id": "1",
        "user_id": 1,
        "updated_at": "2025-04-17T21:41:43.000000Z",
        "created_at": "2025-04-17T21:41:43.000000Z",
        "id": 8
    }
```
### 5. Delete a expense
**DELETE /api/expenses/{expense}**

Delete a specific expense by its ID.

URL Parameters
- expense: The ID of the expense.
### Example  Request

```bash
DELETE /api/expenses/3
```


## Example Response
```json
{
  "message": "expense deleted successfully"
}
```



### 2. Get Audit logs
**GET /api/audits/**


```sql
PUT /api/audits**
```
Gets all audits logs

## Example Response
```json
{
  "message": "expense deleted successfully",
   "data": [
            {
                "id": 8,
                "user_id": 1,
                "company_id": 1,
                "action": "update",
                "changes": "{\n    \"old\": {\n        \"id\": 4,\n        \"company_id\": 1,\n        \"user_id\": 1,\n        \"title\": \"Fuel\",\n        \"amount\": \"8000.00\",\n        \"category\": \"zGas\",\n        \"created_at\": \"2025-04-17T20:41:31.000000Z\",\n        \"updated_at\": \"2025-04-17T21:07:40.000000Z\"\n    },\n    \"new\": {\n        \"title\": \"F1uel\",\n        \"amount\": \"8000\",\n        \"updated_at\": \"2025-04-17 21:27:02\"\n    }\n}",
                "created_at": "2025-04-17T21:27:02.000000Z",
                "updated_at": "2025-04-17T21:27:02.000000Z",
                "user": {
                    "id": 1,
                    "name": "riches Metelewawon",
                    "email": "riches2cute@gmail.com",
                    "role": "Admin",
                    "email_verified_at": null,
                    "created_at": "2025-04-14T12:36:01.000000Z",
                    "updated_at": "2025-04-14T12:36:01.000000Z",
                    "company_id": 1
                }
            },
      

        ],
}
```




<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

---

<!-- ## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details. -->

---

