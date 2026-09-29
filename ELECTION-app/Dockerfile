FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev unzip \
    && docker-php-ext-install pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache database

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["sh", "-c", "touch database/database.sqlite && chown -R www-data:www-data database storage bootstrap/cache && php artisan migrate --force && php artisan db:seed --force && chown -R www-data:www-data database storage bootstrap/cache && php artisan config:cache && apache2-foreground"]
