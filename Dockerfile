FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install Composer dependencies
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# Change ownership of our applications
RUN chown -R www-data:www-data \
    /var/www/html \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Expose port 80 and start Apache server
EXPOSE 80
CMD ["apache2-foreground"]