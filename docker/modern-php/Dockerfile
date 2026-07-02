FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY docker/modern-php/apache.conf /etc/apache2/sites-available/000-default.conf

# Composer PSR-4 autoloader resolves App\ to <baseDir>/modern/src/App.
# vendor is mounted at /var/www/vendor, so baseDir is /var/www.
# App source is bind-mounted at /var/www/html — symlink for autoloading.
RUN ln -sfn /var/www/html /var/www/modern

WORKDIR /var/www/html
