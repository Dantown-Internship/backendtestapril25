# Multi-Tenant SaaS Expense Management API

## Overview
A secure, high-performance, multi-tenant expense management system built with Laravel. This system allows multiple companies to manage their expenses independently through a robust API. It includes secure API authentication, role-based access control, and more.

## Features

- **Multi-Tenant Support**: Complete data isolation between companies
- **Secure API Authentication**: Token-based auth with Laravel Sanctum
- **Role-Based Access Control**: Admin, Manager, and Employee roles
- **Advanced Query Optimization**: Database indexing & eager loading
- **Redis Caching**: High-performance response times
- **Background Job Processing**: Automated weekly expense reports
- **Audit Logging**: Track changes to expense records

## System Requirements

- PHP 8.1 and above
- Composer
- MySQL 8.0+ or PostgreSQL 12+
- Redis Server (optional but recommended)
- Laravel 10+

## Database Setup
This application uses the following database structure:

- **companies** - Stores company information
  
- **users** - Stores user information with company association
  
- **expenses** - Stores expense records
  
- **audit_logs** - Tracks changes to expenses
  
- **jobs** - For queue processing

## Authentication

This API uses Laravel Sanctum for token-based authentication. To authenticate:

Register or login to receive a token
Include the token in the Authorization header:

### Role-Based Permissions

- **Admin**: Full access to all resources
- **Manager**: Can manage expenses but not users
- **Employee**: Can view and create their own expenses


## Background Jobs
The API includes a scheduled job that sends weekly expense reports to company admins.
To set up the scheduler, add this to your server's crontab:

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-repo/expense-management.git
   cd expense-management

**API Endpoints**

## **Authentication**

POST /api/register - Register a new user (Admin only)

POST /api/login - Login and get access token

POST /api/logout - Logout (revoke token)

**Expense Management**
GET /api/expenses - List expenses (paginated, filtered by company)

POST /api/expenses - Create new expense

GET /api/expenses/{id} - Get expense details

PUT /api/expenses/{id} - Update expense (Managers & Admins)

DELETE /api/expenses/{id} - Delete expense (Admins only)

**User Management (Admin only)**
GET /api/users - List users in company

POST /api/users - Add new user

PUT /api/users/{id} - Update user role

DELETE /api/users/{id} - Delete user

## Security Features

This API implements several security features:

- **Data Isolation**: Strict separation between company data
- **RBAC**: Role-based access control for all operations
- **Input Validation**: Thorough validation on all inputs
- **Token Authentication**: Secure API token authentication
- **Audit Logging**: Tracking of all significant data changes
