FROM php:8.1-cli

# System dependencies
RUN apt-get update && apt-get install -y libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl
