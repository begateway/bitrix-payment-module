FROM php:7.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip vim less libgd-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev \
    libxpm-dev libfreetype6-dev && \
    docker-php-ext-configure gd \
    --with-gd \
    --with-webp-dir \
    --with-jpeg-dir \
    --with-png-dir \
    --with-zlib-dir \
    --with-xpm-dir \
    --with-freetype-dir && \
    docker-php-ext-install mysqli zip gd && \
    apt-get remove -y libzip-dev libgd-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev \
    libxpm-dev libfreetype6-dev && \
    rm -rf /var/lib/apt/lists/* && \
    /usr/sbin/a2enmod rewrite

#ADD business_encode.tar.gz /var/www/html/
ADD https://www.1c-bitrix.ru/download/business_encode.tar.gz /var/www/html/

ADD ./configs/php_bitrix.ini /usr/local/etc/php/conf.d/

RUN mkdir -p /var/www/html/bitrix/php_interface/include/sale_payment/ && \
    chown -R www-data.www-data /var/www/html

VOLUME ["/var/www/html"]

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
