#!/bin/sh

# Update composer dependencies
composer update

# Start Symfony server
symfony server:start --port=8000 --no-tls --allow-http

# Create database and load fixtures
php bin/console doctrine:database:create --if-not-exists

# Migrate and load fixtures
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction