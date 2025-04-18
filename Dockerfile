# # Dockerfile

# FROM php:8.2-fpm

# # Install system dependencies
# # RUN apt-get update && apt-get install -y \
# #     libzip-dev zip unzip git curl sqlite3 \
# #     && docker-php-ext-install pdo pdo_sqlite zip
# RUN apt-get update && apt-get install -y \
#     libzip-dev zip unzip git curl \
#     sqlite3 libsqlite3-dev pkg-config \
#     && docker-php-ext-install pdo pdo_sqlite zip


# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www

# # Copy project files
# COPY . .

# # Install dependencies
# RUN composer install --no-dev --optimize-autoloader

# # Set permissions
# RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# # CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
# # CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"]
# # CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8080"]
# CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"]


FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev \
    pkg-config \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --no-dev

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Create SQLite database file if using SQLite
RUN touch database/database.sqlite \
    && chown -R www-data:www-data database/database.sqlite \
    && chmod 664 database/database.sqlite

# Create .env file from .env.example if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env || echo "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\nDB_CONNECTION=sqlite\nDB_DATABASE=/var/www/database/database.sqlite" > .env; fi

# Generate application key
RUN php artisan key:generate --force

# Run migrations and start server
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8080"]
