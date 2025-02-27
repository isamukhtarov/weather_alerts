FROM php:8.2-fpm

WORKDIR /var/www/weather-alerts

RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

COPY --chown=www:www composer.lock composer.json /var/www/weather-alerts/

COPY composer.lock composer.json /var/www/weather-alerts/

USER root

RUN apt-get update && apt-get install -y --no-install-recommends \
    ntpdate \
    netcat-openbsd \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    nano \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN getent group www || groupadd -g 1000 www

RUN id -u www &>/dev/null || useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www/weather-alerts
COPY --chown=www:www . /var/www/weather-alerts

RUN chown www:www /var/www/weather-alerts/entrypoint.sh

RUN chmod +x /var/www/weather-alerts/entrypoint.sh

USER www

EXPOSE 9000
ENTRYPOINT ["/var/www/weather-alerts/entrypoint.sh"]
