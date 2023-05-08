FROM php:8.0-alpine

# Installation des dépendances nécessaires
RUN apk update \
    && apk add --no-cache bash git zip unzip

# Configuration de l'environnement de production
ENV APP_ENV=prod

# Création du répertoire de l'application
WORKDIR /var/www/

# Copie des fichiers de l'application dans le répertoire de travail
COPY . /var/www/

# Installation des dépendances avec Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-progress --no-suggest --no-interaction --no-scripts

# Configuration du serveur web Nginx
RUN apk add --no-cache nginx
COPY ./nginx.conf /etc/nginx/nginx.conf
RUN mkdir -p /run/nginx

# Exposition du port 8080 pour l'écoute de Nginx
EXPOSE 8080

# Définition de la commande pour lancer l'application
CMD ["sh", "-c", "php-fpm && nginx -g 'daemon off;'"] 