#!/bin/bash

# Function to print section headers
print_section() {
    echo "=========================================="
    echo "  $1"
    echo "=========================================="
}

# Install PHP dependencies
print_section "Installing PHP dependencies"
composer install

if [ ! -f .env ]; then
    print_section "Creating .env file from .env.example"
    cp .env.example .env
    echo "Created .env file"
else
    print_section ".env file already exists"
fi

# Generate application key
print_section "Generating application key"
php artisan key:generate

# Run database migrations
print_section "Running database migrations"
php artisan migrate

# Running Database Seeders
print_section "Running database seeders"
php artisan db:seed

print_section "Setup complete! Your Laravel application is running at http://localhost:8000"
echo ""
echo "Services running automatically"
echo ""
