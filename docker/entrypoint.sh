#!/bin/bash

# Salir si hay error
set -e

# 1. Caché de configuración y rutas
echo "🔥 Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Migraciones (Base de datos)
echo "🚀 Ejecutando migraciones..."
php artisan migrate --force

# 3. Enlace simbólico (Storage)
if [ ! -L public/storage ]; then
    echo "🔗 Creando enlace simbólico..."
    php artisan storage:link
fi

# 4. Permisos finales (por si acaso)
chown -R www-data:www-data storage bootstrap/cache

# 5. Iniciar Nginx y PHP-FPM
echo "✅ Servidor listo!"
service nginx start
php-fpm
