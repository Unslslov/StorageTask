# PHP 8.1 FPM на Alpine
FROM php:8.1-fpm-alpine

# Устанавливаем пакеты и расширения, необходимые для Laravel + MySQL
RUN set -eux; \
    apk add --no-cache \
      git curl zip unzip icu-dev oniguruma-dev \
      libzip-dev libxml2-dev bash mariadb-client mariadb-dev; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j$(nproc) \
      intl mbstring pcntl bcmath opcache pdo pdo_mysql mysqli; \
    \
    rm -rf /tmp/pear

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
