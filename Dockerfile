FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmariadb-dev-compat \
    libmariadb-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Redis extension for PHP from PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Copy application code to the container
COPY ./expense-manager /var/www/html

# Expose port for PHP-FPM
EXPOSE 9000

# Set working directory
WORKDIR /var/www/html

# Start PHP-FPM
CMD ["php-fpm"]
