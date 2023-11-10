FROM php:8.0-fpm
WORKDIR /app

# Mise à jour et installation des dépendances
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    wget

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Installer Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
