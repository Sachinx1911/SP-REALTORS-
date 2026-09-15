FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
		git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd exif \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite headers expires deflate

# Don't advertise the exact Apache/PHP version. The "zz-" prefix makes this
# load after Debian's security.conf, which would otherwise reset ServerTokens.
RUN printf 'ServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-available/zz-security-tokens.conf \
	&& a2enconf zz-security-tokens \
	&& printf 'expose_php = Off\n' > /usr/local/etc/php/conf.d/hide-version.ini
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
	&& sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
