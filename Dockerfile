FROM php:8.2-apache

# Install system dependencies required for PostgreSQL and MySQL PDO
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP PDO extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

COPY . /var/www/html/
EXPOSE 80
