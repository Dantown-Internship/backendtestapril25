# Vendor Invoice Management System

A solution for that helps vendors who need to collect payments for services/goods
rendered – are able to request for these payments via invoices. The solution should allow for managing
corporates, their vendors, creation of invoices against one or more vendors, tracking of a vendor's
invoice and nally marking an invoice as paid


## Required Versions

- PHP 8.2
- Laravel 12.x

## Installation Steps

1. **Clone and Setup Project**
   ```bash
   # Clone the repository
   git clone git@github.com:kingsleyudenewu/kingsley-udenewu.git
   cd vendor-invoice
   
   # Install composer dependencies and NPM packages
    composer install
   
   # Install all dependencies in one command
   chmod +x setup.sh
   
   # Run the setup command and allow docker and every dependecies to be installed properly
    ./setup.sh
   
   # update the database credentials on your .env file
   DB_CONNECTION=xxxx
   DB_HOST=xx
   DB_PORT=1234
   DB_DATABASE=xxxx
   DB_USERNAME=xxxx
   DB_PASSWORD=xxxx
   
   Also add your push credentials to the .env file
   '''

### Base URL
`https://localhost:8000/`
