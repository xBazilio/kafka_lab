# syntax=docker/dockerfile:1
FROM php:7.4-buster
RUN ["apt", "update"]
RUN ["apt", "install", "-y", "unzip", "wget"]
RUN mkdir -p /opt/librdkafka-src
WORKDIR "/opt/librdkafka-src"
RUN ["wget", "https://github.com/edenhill/librdkafka/archive/refs/tags/v1.7.0.zip"]
RUN unzip "v1.7.0.zip"
WORKDIR "/opt/librdkafka-src/librdkafka-1.7.0"
RUN ./configure --install-deps && make && make install
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug mysqli pcntl rdkafka
USER 1000:1000
VOLUME ["/var/www"]
WORKDIR "/var/www"