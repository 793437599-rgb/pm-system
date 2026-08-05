FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    libicu-dev \
    libssl-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    opcache \
    && pecl install redis amqp \
    && docker-php-ext-enable redis amqp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY php.ini /usr/local/etc/php/php.ini
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    || echo "Composer install skipped - run manually"

RUN mkdir -p config/jwt var/cache var/log \
    && chmod -R 777 var/cache var/log

EXPOSE 9000

CMD ["php-fpm"]
