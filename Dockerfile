FROM php:8.2-apache

# Installation des extensions PostgreSQL pour PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Activation du module de réécriture Apache
RUN a2enmod rewrite

# Copie du code source dans le conteneur
COPY . /var/www/html/

# Configuration dynamique du port pour Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

WORKDIR /var/www/html/
