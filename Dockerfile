# ============================================
# Dockerfile - Folio (Personal Portfolio Showcase)
# Stack: Laravel 11, PHP 8.2-FPM, PostgreSQL
# ============================================

FROM php:8.2-fpm

ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libexif-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        exif \
        mbstring \
        bcmath \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# Copy composer files dulu (caching layer)
COPY composer.json ./

# Install dependencies (Sanctum sudah ada di composer.json, otomatis ter-install)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy seluruh project
COPY . .

# Generate autoloader + jalankan package discovery
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
