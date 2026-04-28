FROM php:8.2-cli

# Install system dependencies INCLUDING SSL
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libssl-dev \
    && docker-php-ext-install zip

# Enable OpenSSL (already built-in, but ensure it's active)
RUN docker-php-ext-install openssl

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Ignore MongoDB requirement during build
RUN composer install --ignore-platform-req=ext-mongodb

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000"]
