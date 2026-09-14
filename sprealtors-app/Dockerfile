FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
		git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd exif \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
	&& sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
