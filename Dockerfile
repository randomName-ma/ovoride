FROM php:8.3-fpm


RUN apt-get update && apt-get install -y \
    git curl libzip-dev unzip libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip
RUN docker-php-ext-install bcmath gd pdo_mysql mbstring exif pcntl zip

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

COPY . /var/www
WORKDIR /var/www


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
