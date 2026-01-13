#!/bin/bash

# Script para configurar SSL com Let's Encrypt

echo "🔒 Configurando SSL com Let's Encrypt..."
echo ""

read -p "Digite o domínio do backend (ex: api.seudominio.com): " BACKEND_DOMAIN
read -p "Digite o domínio do frontend (ex: seudominio.com): " FRONTEND_DOMAIN
read -p "Digite seu email: " EMAIL

# Instalar Certbot
echo "📦 Instalando Certbot..."
sudo apt update
sudo apt install -y certbot python3-certbot-nginx

# Gerar certificados
echo "🔐 Gerando certificados SSL..."

# Backend
sudo certbot certonly --standalone -d $BACKEND_DOMAIN --email $EMAIL --agree-tos --non-interactive

# Frontend
sudo certbot certonly --standalone -d $FRONTEND_DOMAIN --email $EMAIL --agree-tos --non-interactive

# Criar diretório para certificados
mkdir -p nginx/ssl

# Copiar certificados
echo "📋 Copiando certificados..."
sudo cp /etc/letsencrypt/live/$BACKEND_DOMAIN/fullchain.pem nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/$BACKEND_DOMAIN/privkey.pem nginx/ssl/key.pem
sudo chown -R $USER:$USER nginx/ssl

echo ""
echo "✅ Certificados SSL configurados!"
echo ""
echo "📍 Certificados salvos em: nginx/ssl/"
echo "💡 Configure renovação automática: sudo certbot renew --dry-run"
