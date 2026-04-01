FROM php:8.1-cli

# Install system dependencies required by PHP extensions
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libzip-dev \
        unzip \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo \
        pdo_mysql \
        mbstring \
        fileinfo \
        gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy dependency manifest first for better layer caching
COPY composer.json ./

# Install PHP dependencies (no dev, optimised autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy the rest of the application
COPY . .

# Railway injects $PORT at runtime; expose a default for documentation purposes
EXPOSE 8080

# Start the PHP built-in web server using index.php as the entry point
CMD sh -c 'php -S 0.0.0.0:${PORT:-8080} -t /app index.php'
