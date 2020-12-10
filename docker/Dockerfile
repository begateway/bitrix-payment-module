FROM php:7.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip vim less libgd-dev && \
    docker-php-ext-install mysqli zip gd && \
    apt-get remove -y libgd-dev && \
    rm -rf /var/lib/apt/lists/* && \
    /usr/sbin/a2enmod rewrite

ADD business_encode.tar.gz /var/www/html/
#ADD https://www.1c-bitrix.ru/download/business_encode.tar.gz /var/www/html/

ADD date.timezone.ini /usr/local/etc/php/conf.d/

RUN chown -R www-data.www-data /var/www/html

VOLUME ["/var/www/html"]

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
