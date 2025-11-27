# Deploy no Render - Guia de Configuração

Este guia explica como fazer deploy do backend Laravel no Render usando Docker.

## Pré-requisitos

1. Conta no Render (https://render.com)
2. Repositório Git (GitHub, GitLab, etc.) com o código
3. Banco de dados configurado no Render (PostgreSQL ou MySQL)

## Passos para Deploy

### 1. Configurar o Banco de Dados no Render

1. Crie um novo banco de dados PostgreSQL ou MySQL no Render
2. Anote as credenciais de conexão (host, port, database, username, password)

### 2. Criar Web Service no Render

1. No dashboard do Render, clique em "New +" → "Web Service"
2. Conecte seu repositório Git
3. Configure as seguintes opções:

**Configurações Básicas:**
- **Name**: `sem-multas-backend` (ou o nome que preferir)
- **Environment**: `Docker`
- **Region**: Escolha a região mais próxima
- **Branch**: `main` (ou sua branch principal)
- **Root Directory**: `semMultas` (se o Dockerfile estiver na pasta semMultas)

**Configurações Avançadas:**
- **Dockerfile Path**: `semMultas/Dockerfile` (ou apenas `Dockerfile` se já estiver na raiz)
- **Docker Context**: `.` (ponto)

### 3. Variáveis de Ambiente

Adicione as seguintes variáveis de ambiente no Render:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:... (gere com: php artisan key:generate --show)
APP_DEBUG=false
APP_URL=https://seu-app.onrender.com

# Database
DB_CONNECTION=pgsql (ou mysql)
DB_HOST=seu-host-do-render
DB_PORT=5432 (ou 3306 para MySQL)
DB_DATABASE=nome-do-banco
DB_USERNAME=usuario
DB_PASSWORD=senha

# JWT (se estiver usando)
JWT_SECRET=seu-jwt-secret
JWT_TTL=60

# Outras configurações
LOG_CHANNEL=stack
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_DRIVER=file
```

**Importante:**
- Gere o `APP_KEY` localmente com `php artisan key:generate --show` e copie o valor
- Use as credenciais do banco de dados criado no Render
- Ajuste `APP_URL` para a URL do seu serviço no Render

### 4. Build e Deploy

1. Render detectará automaticamente o Dockerfile
2. O build será executado automaticamente
3. Após o build, o serviço estará disponível na URL fornecida pelo Render

### 5. Executar Migrações (Opcional)

Você pode executar migrações de duas formas:

**Opção 1: Via Shell do Render**
1. No dashboard do serviço, vá em "Shell"
2. Execute: `php artisan migrate --force`

**Opção 2: Automático no Entrypoint**
Descomente a linha no `docker-entrypoint.sh`:
```bash
php artisan migrate --force || true
```

## Estrutura de Arquivos

```
semMultas/
├── Dockerfile              # Dockerfile para produção
├── docker-entrypoint.sh    # Script de inicialização
├── .dockerignore           # Arquivos ignorados no build
└── ... (resto do projeto Laravel)
```

## Troubleshooting

### Erro: "APP_KEY não definido"
- Certifique-se de que a variável `APP_KEY` está configurada no Render
- Ou descomente a linha `php artisan key:generate --force` no `docker-entrypoint.sh`

### Erro: "Database connection failed"
- Verifique as credenciais do banco de dados
- Certifique-se de que o banco está acessível (não use `localhost`, use o host do Render)

### Erro: "Permission denied" no storage
- O Dockerfile já configura as permissões corretas
- Se persistir, verifique os logs do build

### Porta não configurada
- Render define automaticamente a variável `PORT`
- O Dockerfile já está configurado para usar essa variável

## Comandos Úteis

**Ver logs:**
```bash
# No dashboard do Render, vá em "Logs"
```

**Executar comandos Artisan:**
```bash
# No Shell do Render
php artisan [comando]
```

**Reiniciar serviço:**
```bash
# No dashboard, clique em "Manual Deploy" → "Clear build cache & deploy"
```

## Notas Importantes

1. **Storage**: Arquivos em `storage/` são voláteis. Para persistência, use S3 ou outro serviço de storage
2. **Cache**: O cache é limpo e recriado a cada deploy
3. **Migrações**: Execute manualmente ou configure para executar automaticamente
4. **SSL**: Render fornece SSL automático para domínios `.onrender.com`

## Suporte

Para mais informações, consulte a [documentação do Render](https://render.com/docs).

