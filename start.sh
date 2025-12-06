#!/usr/bin/env bash

set -e

echo "=== Starting Budget Tracking System ==="
echo "PORT: ${PORT:-8080}"
echo "DB_HOST: $DB_HOST"

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Optimizing application ==="
php artisan optimize

echo "=== Starting PHP web server ==="
php -S 0.0.0.0:${PORT:-8080} -t public
