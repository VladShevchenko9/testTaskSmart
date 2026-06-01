FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    libjpeg-dev libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip exif

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
