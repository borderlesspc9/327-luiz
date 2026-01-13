# 🚀 Guia de Deploy em VPS

Este guia explica como fazer deploy do sistema em uma VPS (Virtual Private Server).

## 📋 Pré-requisitos

- VPS com Ubuntu 20.04+ ou Debian 11+
- Acesso root ou usuário com sudo
- Domínio configurado (opcional, mas recomendado)
- Certificado SSL (Let's Encrypt recomendado)

## 🔧 Passo 1: Configurar o Servidor

### 1.1 Atualizar o sistema

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Instalar Docker e Docker Compose

```bash
# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verificar instalação
docker --version
docker-compose --version
```

### 1.3 Instalar Git

```bash
sudo apt install git -y
```

## 📥 Passo 2: Clonar o Repositório

```bash
cd /var/www
sudo git clone https://github.com/borderlesspc9/327-luiz.git
sudo chown -R $USER:$USER 327-luiz
cd 327-luiz
```

## ⚙️ Passo 3: Configurar Variáveis de Ambiente

### 3.1 Backend

```bash
cd semMultas
cp .env.example .env
nano .env
```

Configure as seguintes variáveis:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.seudominio.com
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
JWT_SECRET=sua_chave_secreta_aqui
```

### 3.2 Frontend

```bash
cd ../semMultasWeb
cp .env-example .env
nano .env
```

Configure:

```env
VITE_API_URL=https://api.seudominio.com/api
```

## 🔒 Passo 4: Configurar SSL (HTTPS)

### 4.1 Instalar Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 4.2 Gerar Certificado SSL

```bash
# Para o backend
sudo certbot certonly --standalone -d api.seudominio.com

# Para o frontend
sudo certbot certonly --standalone -d seudominio.com
```

### 4.3 Copiar certificados para o projeto

```bash
cd /var/www/327-luiz
mkdir -p nginx/ssl

# Copiar certificados do backend
sudo cp /etc/letsencrypt/live/api.seudominio.com/fullchain.pem nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/api.seudominio.com/privkey.pem nginx/ssl/key.pem
sudo chown -R $USER:$USER nginx/ssl
```

## 🚀 Passo 5: Fazer Deploy

### 5.1 Executar script de deploy

```bash
chmod +x deploy.sh
./deploy.sh
```

### 5.2 Ou manualmente

```bash
# Build e iniciar containers
docker-compose -f docker-compose.prod.yml up -d --build

# Executar migrações
docker exec semmultas_backend php artisan migrate --force
docker exec semmultas_backend php artisan db:seed --force

# Gerar chave da aplicação
docker exec semmultas_backend php artisan key:generate --force

# Otimizar Laravel
docker exec semmultas_backend php artisan config:cache
docker exec semmultas_backend php artisan route:cache
docker exec semmultas_backend php artisan view:cache
```

## 🔥 Passo 6: Configurar Firewall

```bash
# Permitir portas necessárias
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw allow 3000/tcp  # Frontend (se necessário)
sudo ufw enable
```

## 📊 Passo 7: Monitoramento

### 7.1 Ver logs

```bash
# Todos os serviços
docker-compose -f docker-compose.prod.yml logs -f

# Serviço específico
docker-compose -f docker-compose.prod.yml logs -f backend
docker-compose -f docker-compose.prod.yml logs -f frontend
```

### 7.2 Verificar status

```bash
docker-compose -f docker-compose.prod.yml ps
```

## 🔄 Passo 8: Atualizações

### 8.1 Atualizar código

```bash
cd /var/www/327-luiz
git pull origin main
./deploy.sh
```

### 8.2 Renovar certificado SSL

```bash
sudo certbot renew
# Reiniciar nginx
docker-compose -f docker-compose.prod.yml restart nginx
```

## 🗄️ Passo 9: Backup Automático

O sistema já está configurado para fazer backup automático do banco de dados a cada 24 horas. Os backups ficam em `./backups/` e são mantidos por 7 dias.

### Backup manual

```bash
docker exec semmultas_backup sh -c "cp /database/database.sqlite /backups/manual_$(date +%Y%m%d_%H%M%S).sqlite"
```

## 🛠️ Troubleshooting

### Containers não iniciam

```bash
# Ver logs de erro
docker-compose -f docker-compose.prod.yml logs

# Verificar permissões
sudo chown -R $USER:$USER semMultas/storage
sudo chown -R $USER:$USER semMultas/bootstrap/cache
```

### Erro de permissão no banco

```bash
chmod 666 semMultas/database/database.sqlite
```

### Limpar tudo e recomeçar

```bash
docker-compose -f docker-compose.prod.yml down -v
docker system prune -a
./deploy.sh
```

## 📝 Notas Importantes

1. **Segurança**: Sempre use HTTPS em produção
2. **Backup**: Configure backups regulares do banco de dados
3. **Monitoramento**: Configure alertas para falhas
4. **Atualizações**: Mantenha o sistema atualizado
5. **Firewall**: Configure adequadamente o firewall

## 🔗 URLs de Acesso

Após o deploy:

- **Frontend**: https://seudominio.com
- **Backend API**: https://api.seudominio.com/api
- **Backend**: https://api.seudominio.com

## 📞 Suporte

Em caso de problemas, verifique:
1. Logs dos containers
2. Status dos serviços
3. Configurações de firewall
4. Certificados SSL
