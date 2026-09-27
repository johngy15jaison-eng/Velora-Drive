FROM dunglas/frankenphp:php8.4

# Install required PHP extensions
RUN install-php-extensions mysqli pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Application directory
WORKDIR /app

# Copy project files
COPY . /app

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Start Velora Drive
CMD ["sh", "-c", "frankenphp php-server --listen :$PORT --root /app"]