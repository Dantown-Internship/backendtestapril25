# Expense Management API - Setup Guide

## 📌 Prerequisites
Before setting up the project, ensure you have:
- PHP **8.1+**
- Composer installed ([Download Composer](https://getcomposer.org/))
- MySQL or PostgreSQL database
- Mail credentials for testing email functionality

---
Install Dependencies
composer install

Configure Mail Credentials
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="Expense Manager"

Run Database Migrations
php artisan migrate

Start Laravel Server
php artisan serve

Name: Iseru Nelson
Email: nelsoniseru08@gmail.com
Phone:09026915561