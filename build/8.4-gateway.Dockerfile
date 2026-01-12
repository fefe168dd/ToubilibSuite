FROM php:8.4-cli
WORKDIR /var/gateway
COPY ./gateway /var/gateway
RUN apt-get update \
    && apt-get install -y libzip-dev unzip git \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/gateway/public"]
