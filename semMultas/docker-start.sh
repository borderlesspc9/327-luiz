#!/bin/sh

# Obter porta da variável de ambiente PORT (Render define isso automaticamente)
# Fallback para 8000 se não estiver definida
PORT=${PORT:-8000}

echo "🌐 Starting Laravel server on port $PORT"

# Iniciar o servidor PHP
exec php artisan serve --host=0.0.0.0 --port=$PORT

