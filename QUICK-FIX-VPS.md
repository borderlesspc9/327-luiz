# ⚡ Correção Rápida - Erro de Build na VPS

## 🔴 Problema
Build do frontend falhando com erro no `npm run build`.

## ✅ Solução Rápida

### Passo 1: Editar docker-compose.yml na VPS

Edite o arquivo `docker-compose.yml` na sua VPS e certifique-se de que o frontend tem o ARG configurado:

```yaml
frontend:
  build:
    context: ./semMultasWeb
    dockerfile: Dockerfile
    args:
      - VITE_API_URL=${VITE_API_URL:-http://localhost:8000/api}
```

**OU** se estiver usando `docker-compose.prod.yml`:

```yaml
frontend:
  build:
    context: ./semMultasWeb
    dockerfile: Dockerfile
    args:
      - VITE_API_URL=${VITE_API_URL:-https://api.seudominio.com/api}
```

### Passo 2: Definir variável de ambiente (recomendado)

Antes de fazer build, exporte a URL da API:

```bash
# Se usar HTTP
export VITE_API_URL=http://seu-ip:8000/api

# Se usar HTTPS
export VITE_API_URL=https://api.seudominio.com/api

# Depois fazer build
docker-compose up --build
```

### Passo 3: Se ainda falhar - Build Manual

```bash
# 1. Entrar na pasta do frontend
cd semMultasWeb

# 2. Criar arquivo .env.production
echo "VITE_API_URL=http://seu-ip:8000/api" > .env.production

# 3. Instalar e buildar
npm install
npm run build-only

# 4. Voltar e fazer build do Docker
cd ..
docker-compose up --build
```

## 🎯 Comando Completo (Copy & Paste)

```bash
# Na raiz do projeto na VPS
export VITE_API_URL=http://$(hostname -I | awk '{print $1}'):8000/api
docker-compose down
docker-compose build --no-cache frontend
docker-compose up -d
```

## 📝 Verificar se Funcionou

```bash
# Ver logs
docker-compose logs frontend

# Verificar container
docker ps | grep frontend

# Testar acesso
curl http://localhost:5173
```

## 🔍 Se Ainda Não Funcionar

1. **Verificar logs detalhados:**
   ```bash
   docker-compose logs --tail=100 frontend
   ```

2. **Limpar tudo e recomeçar:**
   ```bash
   docker-compose down -v
   docker system prune -a
   export VITE_API_URL=http://seu-ip:8000/api
   docker-compose up --build
   ```

3. **Verificar se o Dockerfile foi atualizado:**
   ```bash
   cat semMultasWeb/Dockerfile | grep VITE_API_URL
   ```

## 💡 Dica

Se você estiver usando um serviço de deploy automático (como Hostinger, Railway, etc.), configure a variável de ambiente `VITE_API_URL` no painel de controle do serviço antes de fazer o deploy.
