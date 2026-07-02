FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY docker/modern-php/apache.conf /etc/apache2/sites-available/000-default.conf

# The Composer PSR-4 autoloader (generated at the repo root) resolves `App\` to
# `<baseDir>/modern/src/App`, where baseDir is the parent of the vendor dir.
# In this container vendor is mounted at /var/www/vendor, so baseDir is /var/www
# and the autoloader looks for /var/www/modern. The app source is bind-mounted at
# /var/www/html, so expose it at /var/www/modern via a symlink for autoloading.
RUN ln -sfn /var/www/html /var/www/modern

WORKDIR /var/www/html
