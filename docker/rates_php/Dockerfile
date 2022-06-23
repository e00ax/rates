FROM devilbox/php-fpm-8.1:latest

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
		libzip-dev \
        libpcre3 \
        libpcre3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install exif bcmath mysqli pdo_mysql pcntl \
    && docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install zip

#######################################################################################
# Misc options
#######################################################################################
# Add PHP-FPM config: UDS
ADD uds.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Add PHP-FPM config: TCP
# ADD tcp.conf /usr/local/etc/php-fpm.d/www.conf

# Create web user and group
RUN addgroup --gid 1000 laravel
RUN adduser --gid 1000 --shell /bin/sh --debug laravel

# Create web directory
RUN mkdir -p /var/www/html

RUN chown laravel:laravel /var/www/html

# Install Composer
COPY --from=composer:2.1.8 /usr/bin/composer /usr/bin/composer