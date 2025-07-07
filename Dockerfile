FROM php:8.1-cli

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libbrotli-dev \
    && apt-get clean

# Установка PHP-расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath sockets

# Установка Swoole (один раз!)
RUN pecl install -n swoole-6.0.2 --configureoptions '--enable-brotli=no' && \
    docker-php-ext-enable swoole

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем приложение
COPY . .

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Публикация конфигов Octane
RUN php artisan vendor:publish --tag=octane-config

# Команда запуска Octane
CMD ["php", "artisan", "octane:start", "--server=swoole"]
