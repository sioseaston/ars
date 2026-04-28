FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    ca-certificates \
    git \
    libssl-dev \
    libzip-dev \
    pkg-config \
    unzip \
    && docker-php-ext-install zip \
    && pecl install mongodb-1.21.5 \
    && docker-php-ext-enable mongodb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000"]
