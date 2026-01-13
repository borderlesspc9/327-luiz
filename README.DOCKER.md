# 🐳 Docker Setup - Sistema Sem Multas

Este projeto pode ser executado usando Docker Compose para facilitar o desenvolvimento e deploy.

## 📋 Pré-requisitos

- Docker Desktop instalado (Windows/Mac) ou Docker Engine + Docker Compose (Linux)
- Git (para clonar o repositório)

## 🚀 Executando com Docker

### Setup Inicial (Primeira vez)

```bash
# Windows
docker-setup.bat

# Linux/Mac
chmod +x docker-setup.sh
./docker-setup.sh
```

### Opção 1: Modo Desenvolvimento (Recomendado)

Este modo monta os volumes e permite hot-reload:

```bash
# Windows
docker-start.bat

# Linux/Mac
chmod +x docker-start.sh
./docker-start.sh

# Ou manualmente:
docker-compose -f docker-compose.dev.yml up --build
```

**Acessos:**
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api
- Backend: http://localhost:8000

### Opção 2: Modo Produção

Este modo faz build das aplicações:

```bash
# Windows
docker-start.bat prod

# Linux/Mac
./docker-start.sh prod

# Ou manualmente:
docker-compose up --build
```

**Acessos:**
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api

## 🔧 Configuração Inicial

Após iniciar os containers, você precisa configurar o backend:

```bash
# Entrar no container do backend
docker exec -it semmultas_backend bash

# Ou se estiver usando dev:
docker exec -it semmultas_backend_dev bash

# Dentro do container, executar:
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

## 📝 Variáveis de Ambiente

### Backend (.env)
Crie um arquivo `.env` na pasta `semMultas/`:

```env
APP_NAME="Sem Multas"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

JWT_SECRET=
```

### Frontend (.env)
Crie um arquivo `.env` na pasta `semMultasWeb/`:

```env
VITE_API_URL=http://localhost:8000/api
```

## 🛠️ Comandos Úteis

### Parar os containers
```bash
docker-compose down
# ou
docker-compose -f docker-compose.dev.yml down
```

### Ver logs
```bash
docker-compose logs -f
# ou logs de um serviço específico
docker-compose logs -f backend
docker-compose logs -f frontend
```

### Rebuild após mudanças
```bash
docker-compose up --build
```

### Executar comandos no backend
```bash
docker exec -it semmultas_backend php artisan migrate
docker exec -it semmultas_backend php artisan tinker
```

### Limpar tudo e recomeçar
```bash
docker-compose down -v
docker-compose up --build
```

## 🗄️ Banco de Dados

O projeto usa SQLite por padrão. O arquivo `database.sqlite` será criado automaticamente na pasta `semMultas/database/`.

Se quiser usar MySQL, descomente a seção `db` no `docker-compose.yml` e ajuste o `.env` do backend.

## 🔍 Troubleshooting

### Porta já em uso
Se as portas 8000 ou 5173 estiverem ocupadas, altere no `docker-compose.yml`:
```yaml
ports:
  - "8001:8000"  # Mude 8000 para outra porta
```

### Permissões no Linux
```bash
sudo chown -R $USER:$USER semMultas/storage
sudo chown -R $USER:$USER semMultas/bootstrap/cache
```

### Limpar cache do Docker
```bash
docker system prune -a
```

## 📚 Estrutura

```
327-luiz/
├── docker-compose.yml          # Produção
├── docker-compose.dev.yml      # Desenvolvimento
├── semMultas/
│   ├── Dockerfile
│   ├── Dockerfile.dev
│   └── nginx.conf
└── semMultasWeb/
    ├── Dockerfile
    ├── Dockerfile.dev
    └── nginx.conf
```

## 🚨 Notas Importantes

1. **Desenvolvimento**: Use `docker-compose.dev.yml` para ter hot-reload
2. **Produção**: Use `docker-compose.yml` para build otimizado
3. O banco SQLite é criado automaticamente
4. Certifique-se de que as portas 8000 e 5173 estão livres
