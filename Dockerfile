FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /var/www/html/
WORKDIR /var/www/html

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data /var/www/html

# Generate ignored config.php at runtime from Render environment variables.
# Shell invocation avoids requiring an executable bit in the GitHub-created script.
CMD ["sh", "/var/www/html/docker-entrypoint.sh"]

EXPOSE 80
