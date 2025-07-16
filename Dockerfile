FROM php:8.2-apache

# Instalar extensiones de PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath

# Habilitar módulo rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . /var/www/html/

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias PHP (sin las dev)
RUN composer install --no-dev --optimize-autoloader

# Permisos de almacenamiento
RUN chown -R www-data:www-data storage bootstrap/cache

# Puerto de Apache
EXPOSE 80
