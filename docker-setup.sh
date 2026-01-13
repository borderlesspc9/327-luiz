#!/bin/bash

echo "🔧 Configurando ambiente Docker..."
echo ""

# Criar .env do backend se não existir
if [ ! -f "semMultas/.env" ]; then
    echo "📝 Criando .env do backend..."
    cat > semMultas/.env << EOF
APP_NAME="Sem Multas"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

JWT_SECRET=
EOF
    echo "✅ Arquivo semMultas/.env criado"
else
    echo "ℹ️  Arquivo semMultas/.env já existe"
fi

# Criar .env do frontend se não existir
if [ ! -f "semMultasWeb/.env" ]; then
    echo "📝 Criando .env do frontend..."
    cat > semMultasWeb/.env << EOF
VITE_API_URL=http://localhost:8000/api
EOF
    echo "✅ Arquivo semMultasWeb/.env criado"
else
    echo "ℹ️  Arquivo semMultasWeb/.env já existe"
fi

# Criar diretório de banco de dados
mkdir -p semMultas/database
touch semMultas/database/database.sqlite
chmod 666 semMultas/database/database.sqlite

echo ""
echo "✅ Configuração concluída!"
echo ""
echo "🚀 Agora você pode executar:"
echo "   ./docker-start.sh        (desenvolvimento)"
echo "   ./docker-start.sh prod   (produção)"
echo ""
