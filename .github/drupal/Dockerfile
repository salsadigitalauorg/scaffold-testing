FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure environment
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app

# Copy only required files for dependency installation
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

# Copy remaining source files
COPY . .
