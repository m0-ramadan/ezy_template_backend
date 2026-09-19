#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "[1/7] Laravel dependencies"
composer install --no-dev --optimize-autoloader

echo "[2/7] Database migrations"
php artisan migrate --force

echo "[3/7] Public storage symlink"
php artisan storage:link || true

echo "[4/7] Node dependencies"
npm install

echo "[5/7] Playwright Chromium"
npx playwright install chromium

echo "[6/7] Fetch real Canva media; Office media is already source-specific in this package"
php artisan templates:finalize-media --skip-office

echo "[7/7] Clear caches and validate"
php artisan optimize:clear
php artisan templates:validate-media

echo "Done. Review storage/app/canva-media-sync-report.json for any failed Canva URLs."
