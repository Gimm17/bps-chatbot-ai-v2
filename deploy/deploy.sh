#!/usr/bin/env bash
# ====================================================================
# BPS AI Assistant v2 — Deployment Script for cPanel Shared Hosting
# Target: bps-chatbot-v2.pinnhost.my.id
# ====================================================================

set -e

echo "🚀 Starting deployment of BPS AI Assistant v2..."

# 1. Pull latest code from GitHub
if [ -d ".git" ]; then
    echo "📥 Pulling latest git commits..."
    git pull origin main || git pull origin master
fi

# 2. Install PHP dependencies
echo "📦 Installing composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Create SQLite database file if not exists
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄 Creating database.sqlite..."
    touch database/database.sqlite
fi

# 4. Run database migrations
echo "⚡ Running migrations..."
php artisan migrate --force

# 5. Clear and cache Laravel configuration, routes, and views
echo "🧹 Optimizing Laravel caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Ensure storage permissions
echo "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# 7. Warm up BPS metadata cache
echo "🔥 Pre-warming BPS WebAPI metadata cache..."
php artisan bps:preload || echo "Preload warning: continued."

echo "✅ Deployment to bps-chatbot-v2.pinnhost.my.id completed successfully!"
