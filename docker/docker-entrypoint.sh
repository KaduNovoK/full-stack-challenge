#!/bin/sh

# Corrigir permissões se os diretórios existirem
if [ -d "/var/www/html/storage" ] && [ -d "/var/www/html/bootstrap/cache" ]; then
    echo "Ajustando permissões..."
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
fi

# Rodar o PHP-FPM
exec php-fpm