FROM php:7.4 as composer-cache

RUN apt-get update && apt-get install -y \
  unzip \
  curl \
  git

WORKDIR /build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

RUN composer install --no-dev --optimize-autoloader


FROM nginx:stable-bullseye-perl

RUN apt-get update

RUN apt-get install -y \
  php7.4 \
  php7.4-fpm \
  php7.4-cli \
  php7.4-curl \
  php7.4-mbstring \
  php7.4-zip \
  php7.4-gd \
  php7.4-mysql

RUN apt-get install -y \
    build-essential \
    mysql-client-* \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    gawk \
    default-mysql-client \
    libonig-dev


# RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
# RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
# RUN docker-php-ext-install gd
    
# COPY --from=nginx:stable-bookworm-perl /usr/sbin/nginx /usr/sbin/nginx
# COPY --from=nginx:stable-bookworm-perl /

WORKDIR /var/www/trellis-api

COPY --from=composer-cache /build/vendor ./vendor

# RUN apt-get install -y libgeoip1 libxml2 liblmdb0 libyajl2 libssl-dev libssh2-1-dev

COPY . .

RUN php artisan trellis:download-app --latest --api-endpoint=http://localhost:8000

RUN cp ./docker/conf.d/*.conf /etc/nginx/conf.d/
RUN mv ./docker/default.nginx.conf /etc/nginx/nginx.conf

RUN nginx -t -c /etc/nginx/nginx.conf

EXPOSE 80
EXPOSE 443
EXPOSE 8000
EXPOSE 8443
ENTRYPOINT ["nginx", "-g", "daemon off;"]