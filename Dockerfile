FROM php:8.0-fpm

ARG DEBUG=1

SHELL ["/bin/bash", "-c"]

RUN apt-get update && apt-get install -y libbz2-dev libzip-dev gettext-base procps runit git libfcgi-bin memcached libmemcached-dev
RUN docker-php-ext-install bz2 zip

## Add Composer
COPY --from=composer:2.4.2 /usr/bin/composer /usr/bin/

## Preparing files
RUN rm -rf /etc/service
RUN mkdir /opt/app
COPY ./rootfs /

RUN pecl install memcached \
    && rm -rf /usr/share/php7
RUN docker-php-ext-enable memcached && \
        rm -rf /tmp/*

RUN chmod +x /docker-entrypoint.sh

WORKDIR /opt/app

EXPOSE 9000

## Startup script
CMD ["/docker-entrypoint.sh"]

HEALTHCHECK --start-period=1m  --timeout=10s CMD env -i SCRIPT_NAME="/fpm-ping" REQUEST_METHOD="GET" SCRIPT_FILENAME="/fpm-ping" cgi-fcgi -bind -connect 127.0.0.1:9000 2> /dev/null || exit 1

ENV client_max_body_size=20M \
    clear_env=no \
    allow_url_fopen=On \
    allow_url_include=Off \
    display_errors=Off \
    file_uploads=On \
    max_execution_time=0 \
    max_input_time=-1 \
    max_input_vars=1000 \
    memory_limit=512M \
    post_max_size=20M \
    upload_max_filesize=20M \
    zlib_output_compression=On
