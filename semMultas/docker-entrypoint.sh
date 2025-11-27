#!/bin/sh

set -e

echo "🚀 Starting Laravel application..."

# Verificar se .env existe, se não, criar a partir do .env.example
if [ ! -f .env ]; then
    echo "⚠️  .env file not found, creating from .env.example..."
    cp .env.example .env || true
fi

# Gerar APP_KEY se não existir
php artisan key:generate --force || true

# Limpar cache
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Cache de configuração e rotas para produção
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Executar migrações (opcional - descomente se quiser migrações automáticas)
# php artisan migrate --force || true

echo "✅ Application ready!"

# Executar o comando passado como argumento ou o CMD padrão
exec "$@"

