FROM php:8.3.12-fpm

RUN apt-get update && apt-get install -y \
    git \
    nodejs \
    npm \
    libzip-dev \
    unzip \
    netcat-openbsd \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ./memory_cards /var/www-src

COPY ./docker/scripts/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

CMD ["php-fpm"]