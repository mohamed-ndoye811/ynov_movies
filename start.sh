# Update composer dependencies
composer install
composer update

# Create database and load fixtures
php bin/console doctrine:database:create --if-not-exists

# Migrate and load fixtures
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# Start Symfony server
symfony server:start --port=8000 --no-tls --allow-http