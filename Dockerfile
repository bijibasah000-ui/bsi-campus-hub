FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libxml2-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd mbstring xml \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

RUN mkdir -p storage/framework/sessions \
        storage/framework/cache/data \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Buat startup script
# PENTING: Tidak pakai set -e di level atas karena artisan commands butuh .env
# yang tidak ada saat build time. Setiap command dijalankan independen.
RUN printf '#!/bin/sh\n\
\n\
echo "=== [1/5] Clearing caches ==="\n\
# Jalankan dengan || true agar error tidak crash container\n\
# (artisan mungkin komplain tentang config saat pertama boot)\n\
php artisan config:clear  || true\n\
php artisan cache:clear   || true\n\
php artisan route:clear   || true\n\
php artisan view:clear    || true\n\
\n\
echo "=== [2/5] Waiting for database connection ==="\n\
MAX_TRIES=30\n\
TRIES=0\n\
until php artisan migrate:status > /dev/null 2>&1; do\n\
    TRIES=$((TRIES+1))\n\
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then\n\
        echo "ERROR: Database not reachable after ${MAX_TRIES} attempts."\n\
        exit 1\n\
    fi\n\
    echo "DB not ready yet (attempt $TRIES/$MAX_TRIES), retrying in 2s..."\n\
    sleep 2\n\
done\n\
echo "Database is ready!"\n\
\n\
echo "=== [3/5] Running migrations ==="\n\
php artisan migrate --force\n\
\n\
echo "=== [4/5] Creating storage symlink ==="\n\
php artisan storage:link --force || true\n\
\n\
echo "=== [5/5] Starting server on port ${PORT:-8000} ==="\n\
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}\n\
' > /start.sh && chmod +x /start.sh

EXPOSE 8000

CMD ["/bin/sh", "/start.sh"]
