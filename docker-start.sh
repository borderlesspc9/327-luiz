#!/bin/bash

echo "🚀 Iniciando Sistema Sem Multas com Docker..."
echo ""

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Por favor, inicie o Docker Desktop."
    exit 1
fi

# Verificar se docker-compose está disponível
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose não encontrado. Instale o Docker Compose."
    exit 1
fi

# Escolher modo
MODE=${1:-dev}

if [ "$MODE" = "prod" ]; then
    echo "📦 Modo: PRODUÇÃO"
    docker-compose -f docker-compose.yml up --build -d
else
    echo "🔧 Modo: DESENVOLVIMENTO"
    docker-compose -f docker-compose.dev.yml up --build
fi

echo ""
echo "✅ Servidores iniciados!"
echo ""
echo "📍 Acessos:"
echo "   Frontend: http://localhost:5173"
echo "   Backend:  http://localhost:8000"
echo "   API:      http://localhost:8000/api"
echo ""
echo "💡 Para parar os containers: docker-compose down"
echo "💡 Para ver logs: docker-compose logs -f"
