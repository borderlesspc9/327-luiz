#!/bin/bash

set -e

echo "🚀 Iniciando deploy em produção..."
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se está no diretório correto
if [ ! -f "docker-compose.prod.yml" ]; then
    echo -e "${RED}❌ Erro: docker-compose.prod.yml não encontrado!${NC}"
    exit 1
fi

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker não está rodando!${NC}"
    exit 1
fi

# Verificar variáveis de ambiente
if [ ! -f "semMultas/.env" ]; then
    echo -e "${YELLOW}⚠️  Arquivo .env não encontrado!${NC}"
    echo "Criando .env a partir do exemplo..."
    cp semMultas/.env.example semMultas/.env 2>/dev/null || echo "APP_KEY=" > semMultas/.env
fi

# Backup do banco antes do deploy
echo -e "${YELLOW}📦 Fazendo backup do banco de dados...${NC}"
mkdir -p backups
if [ -f "semMultas/database/database.sqlite" ]; then
    cp semMultas/database/database.sqlite backups/database_backup_$(date +%Y%m%d_%H%M%S).sqlite
    echo -e "${GREEN}✅ Backup criado!${NC}"
fi

# Parar containers existentes
echo -e "${YELLOW}🛑 Parando containers existentes...${NC}"
docker-compose -f docker-compose.prod.yml down || true

# Build das imagens
echo -e "${YELLOW}🔨 Construindo imagens...${NC}"
docker-compose -f docker-compose.prod.yml build --no-cache

# Iniciar containers
echo -e "${YELLOW}🚀 Iniciando containers...${NC}"
docker-compose -f docker-compose.prod.yml up -d

# Aguardar containers iniciarem
echo -e "${YELLOW}⏳ Aguardando containers iniciarem...${NC}"
sleep 10

# Executar migrações e seeders
echo -e "${YELLOW}🗄️  Executando migrações...${NC}"
docker exec semmultas_backend php artisan migrate --force || true
docker exec semmultas_backend php artisan db:seed --force || true

# Gerar chave da aplicação se não existir
echo -e "${YELLOW}🔑 Verificando chave da aplicação...${NC}"
docker exec semmultas_backend php artisan key:generate --force || true

# Otimizar Laravel
echo -e "${YELLOW}⚡ Otimizando Laravel...${NC}"
docker exec semmultas_backend php artisan config:cache || true
docker exec semmultas_backend php artisan route:cache || true
docker exec semmultas_backend php artisan view:cache || true

# Verificar saúde dos containers
echo -e "${YELLOW}🏥 Verificando saúde dos containers...${NC}"
sleep 5

if docker ps | grep -q semmultas_backend && docker ps | grep -q semmultas_nginx && docker ps | grep -q semmultas_frontend; then
    echo -e "${GREEN}✅ Todos os containers estão rodando!${NC}"
else
    echo -e "${RED}❌ Alguns containers não estão rodando!${NC}"
    docker-compose -f docker-compose.prod.yml ps
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Deploy concluído com sucesso!${NC}"
echo ""
echo "📍 Serviços disponíveis:"
echo "   Frontend: http://seu-ip:3000"
echo "   Backend:  http://seu-ip"
echo "   API:      http://seu-ip/api"
echo ""
echo "💡 Para ver logs: docker-compose -f docker-compose.prod.yml logs -f"
echo "💡 Para parar: docker-compose -f docker-compose.prod.yml down"
