# Expense Management System

A robust expense management system built with Laravel, featuring role-based access control, company-based data isolation, and comprehensive expense tracking capabilities.

## Features

- **Authentication & Authorization**
  - Secure user registration and login
  - Role-based access control (Admin, Manager, Employee)
  - Company-based data isolation
  - API token authentication with Laravel Sanctum

- **Expense Management**
  - Create, read, update, and delete expenses
  - Categorize expenses
  - Track expense status
  - Filter and search expenses
  - Pagination for large datasets

- **User Management**
  - Admin can create and manage users
  - Assign roles to users
  - Company-specific user management

- **Audit Logging**
  - Track all expense changes
  - Record who made changes and when
  - Maintain data integrity

- **Weekly Reports**
  - Automated weekly expense reports
  - PDF generation
  - Email delivery to administrators

## Technology Stack

- **Backend**: Laravel 12
- **Database**: MySQL/PostgreSQL/Mariadb/Sqlite
- **Authentication**: Laravel Sanctum
- **API**: RESTful API
- **Testing**: Pest

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or PostgreSQL 9.6+

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/jayflashy/expense-management-system.git
   cd expense-management-system
   cd expense-managemer
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment variables**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your database**
   Edit the `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=expense_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**
   ```bash
   composer run dev
   ```
7. **Run Job sScheduler**
   ```bash 
   php artisan schedule:run
   ```

## API Documentation

Api docs is available at `/docs/api`
  

## Role Permissions

- **Admin**
  - Full access to all features
  - Can manage users
  - Can view all expenses
  - Can generate reports

- **Manager**
  - Can view all expenses
  - Can approve/reject expenses
  - Cannot manage users

- **Employee**
  - Can create and view their own expenses
  - Cannot view other users' expenses
  - Cannot manage users

## Testing

Run the test suite with:

```bash
php artisan test
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

