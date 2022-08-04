FROM php:8.1.5-fpm-alpine3.15

RUN apk add --update --no-cache bash

ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/1.5.33/install-php-extensions /usr/local/bin/

ENV IPE_GD_WITHOUTAVIF=1

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    @composer-2.3.5

RUN adduser -D php && addgroup php www-data

USER php

# COPY --chown=php:www-data . /var/www/html

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html"]