FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    libjpeg-dev libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip exif

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
