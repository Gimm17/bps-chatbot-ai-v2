#!/usr/bin/env bash
set -e

echo "🚀 Setting up BPS AI Assistant v2 on cPanel..."
cd /home/pinnhost/bps-chatbot-v2.pinnhost.my.id

# 1. Create .env file
cat << 'EOF' > .env
APP_NAME="BPS AI Assistant"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://bps-chatbot-v2.pinnhost.my.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=stack
LOG_LEVEL=info

DB_CONNECTION=sqlite
DB_DATABASE=/home/pinnhost/bps-chatbot-v2.pinnhost.my.id/database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120

QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=bps_cache_

# BPS WebAPI Integration
BPS_ENABLED=true
BPS_WEBAPI_KEY=32a4af778c0b74a62c19857b278cab33
BPS_WEBAPI_BASE_URL=https://webapi.bps.go.id
BPS_HTTP_TIMEOUT_SEC=15
BPS_CACHE_ENABLED=true
BPS_CACHE_TTL_HOURS=24

# LimitRouter AI Provider Configuration
AI_DEFAULT_PROVIDER=limitrouter
LIMITROUTER_API_KEY=sk-lr-88443757ed5b6434f5825179cd53fb96eae5b28256716272
LIMITROUTER_BASE_URL=https://limitrouter.com/v1
LIMITROUTER_DEFAULT_MODEL=gemini-3.7-flash
AI_DEMO_MODE=true
AI_TIMEOUT=30

VITE_APP_NAME="${APP_NAME}"
EOF

echo "✅ .env created."

# 2. Install Composer Dependencies (Production)
echo "📦 Installing composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Create SQLite Database file
echo "🗄 Preparing database.sqlite..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# 4. Generate App Key
echo "🔑 Generating Application Key..."
php artisan key:generate --force

# 5. Run Database Migrations
echo "⚡ Running migrations..."
php artisan migrate --force

# 6. Optimize Laravel caches
echo "🧹 Optimizing Laravel caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set storage and cache permissions
echo "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# 8. Pre-index key publications
echo "📚 Pre-indexing strategic publications..."
php artisan bps:index-publications --domain=7200 --keyword=kependudukan --limit=2 || echo "Pre-index notice."

echo "🎉 Deployment setup completed successfully for bps-chatbot-v2.pinnhost.my.id!"
