FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    zlib-dev \
    oniguruma-dev

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_pgsql intl zip opcache amqp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/api

RUN addgroup -g 1000 appuser && adduser -u 1000 -G appuser -D appuser
RUN mkdir -p /var/www/api/var && chown -R appuser:appuser /var/www/api
USER appuser