# syntax=docker/dockerfile:1
FROM php:7.4-buster
RUN ["apt", "update"]
RUN ["apt", "install", "-y", "unzip"]
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug mysqli rdkafka
USER 1000:1000
VOLUME ["/var/www"]
WORKDIR "/var/www"