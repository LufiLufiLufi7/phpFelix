
FROM php:8.2-apache

# Instalacija PHP ekstenzija
RUN docker-php-ext-install pdo_mysql mysqli

# Omogucavanje Apache rewrite modula
RUN a2enmod rewrite

# Instalacija Composer-a
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Kopiranje projekta
COPY . /var/www/html/

WORKDIR /var/www/html

# Instalacija PHP biblioteka
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Dozvole za Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
