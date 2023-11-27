FROM php:8.1-fpm
WORKDIR /app

# Mise à jour et installation des dépendances
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    wget \
    clean
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Installer Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Installer MakerBundle et Symfony ORM Pack
RUN composer require symfony/maker-bundle --dev
RUN composer require symfony/orm-pack
