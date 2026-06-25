#!/bin/sh
set -e

echo "=== [1/5] Clearing caches ==="
php artisan config:clear  || true
php artisan cache:clear   || true
php artisan route:clear   || true
php artisan view:clear    || true

# v2
echo "=== [2/5] Waiting for database connection ==="
MAX_TRIES=30
TRIES=0
until php artisan migrate:status > /dev/null 2>&1; do
    TRIES=$((TRIES+1))
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then
        echo "ERROR: Database not reachable after ${MAX_TRIES} attempts."
        exit 1
    fi
    echo "DB not ready yet (attempt $TRIES/$MAX_TRIES), retrying in 2s..."
    sleep 2
done
echo "Database is ready!"

echo "=== [3/5] Running migrations ==="
php artisan migrate --force

echo "=== [4/5] Creating storage symlink ==="
rm -f public/storage
php artisan storage:link || true

echo "=== [5/5] Starting server on port ${PORT:-8000} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
