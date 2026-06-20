#!/bin/sh

set -e

echo "==> Installing npm dependencies and building assets..."
if [ -f package-lock.json ]; then
    npm ci
else
    npm install
fi
npm run build

echo "==> Downloading Composer..."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --quiet
rm composer-setup.php

echo "==> Installing PHP dependencies..."
php composer.phar install --no-dev --optimize-autoloader --no-interaction

echo "==> Build complete."
