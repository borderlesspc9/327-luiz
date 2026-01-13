# 🔧 Correção do Erro de Build na VPS

## Problema
O build do frontend está falhando com erro no `npm run build`.

## Solução Aplicada

### 1. Dockerfile Corrigido
- Adicionado fallback para `npm ci` com `--legacy-peer-deps`
- Criado arquivo `.env.production` automaticamente durante o build
- Adicionado fallback para diferentes comandos de build
- Melhor tratamento de erros

### 2. Como Usar na VPS

#### Opção 1: Usar variável de ambiente no docker-compose

No seu `docker-compose.yml` ou `docker-compose.prod.yml`, defina a variável:

```yaml
frontend:
  build:
    context: ./semMultasWeb
    dockerfile: Dockerfile
    args:
      - VITE_API_URL=${VITE_API_URL:-http://localhost:8000/api}
```

E antes de fazer build, exporte a variável:

```bash
export VITE_API_URL=https://api.seudominio.com/api
docker-compose up --build
```

#### Opção 2: Editar diretamente no docker-compose

Edite o arquivo `docker-compose.yml` ou `docker-compose.prod.yml` e altere:

```yaml
frontend:
  build:
    context: ./semMultasWeb
    dockerfile: Dockerfile
    args:
      - VITE_API_URL=https://api.seudominio.com/api  # <-- Altere aqui
```

### 3. Build Manual (se ainda falhar)

Se o build ainda falhar, você pode fazer build manual:

```bash
cd semMultasWeb

# Criar .env.production
echo "VITE_API_URL=https://api.seudominio.com/api" > .env.production

# Build manual
npm install
npm run build

# Depois fazer build do Docker
cd ..
docker-compose up --build
```

## Verificação

Após o build, verifique se funcionou:

```bash
# Ver logs do build
docker-compose logs frontend

# Verificar se o container está rodando
docker ps | grep frontend
```

## Troubleshooting

### Erro: "VITE_API_URL is not defined"
- Certifique-se de passar o ARG no docker-compose
- Verifique se o arquivo `.env.production` foi criado

### Erro: "npm run build failed"
- Tente usar `npm run build-only` diretamente
- Verifique se todas as dependências foram instaladas

### Erro: "Cannot find module"
- Limpe o cache: `docker system prune -a`
- Rebuild sem cache: `docker-compose build --no-cache`
