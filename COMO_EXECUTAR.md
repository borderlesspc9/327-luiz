# 🚀 Como Executar o Projeto

Este projeto possui duas partes: **Backend (Laravel)** e **Frontend (Vue.js)**.

## ✅ Frontend Vue.js (semMultasWeb)

O frontend já está configurado e rodando! 

**Status:** ✅ Executando em background

Para executar manualmente:
```powershell
cd semMultasWeb
npm install
npm run dev
```

O servidor de desenvolvimento estará disponível em: `http://localhost:5173` (ou outra porta indicada no terminal)

---

## ⚙️ Backend Laravel (semMultas)

Para executar o backend, você precisa ter **PHP 8.2+** e **Composer** instalados.

### Opção 1: Instalar PHP e Composer no Windows

1. **Instalar PHP:**
   - Baixe o PHP 8.2+ de: https://windows.php.net/download/
   - Extraia em `C:\php`
   - Adicione `C:\php` ao PATH do sistema
   - Reinicie o terminal

2. **Instalar Composer:**
   - Baixe de: https://getcomposer.org/download/
   - Execute o instalador
   - Ou use: `php composer.phar` (já existe no projeto)

3. **Configurar o projeto:**
   ```powershell
   cd semMultas
   
   # Usar o composer.phar local
   php composer.phar install
   
   # Copiar arquivo de ambiente
   copy .env.example .env
   
   # Gerar chave da aplicação
   php artisan key:generate
   
   # Executar migrações (se necessário)
   php artisan migrate
   
   # Iniciar servidor
   php artisan serve
   ```

### Opção 2: Usar Laravel Sail (Docker) - RECOMENDADO

Se você tem Docker instalado, use o Laravel Sail:

```powershell
cd semMultas

# Instalar dependências (usando composer.phar local)
php composer.phar install

# Configurar Sail
php artisan sail:install

# Iniciar containers
.\vendor\bin\sail up -d

# Ou se preferir usar diretamente:
docker-compose up -d
```

### Opção 3: Usar XAMPP/WAMP

1. Instale XAMPP ou WAMP
2. Configure o PHP no PATH
3. Siga os passos da Opção 1

---

## 📝 Resumo Rápido

**Frontend (já rodando):**
```powershell
cd semMultasWeb
npm run dev
```

**Backend (requer PHP):**
```powershell
cd semMultas
php composer.phar install
php artisan serve
```

---

## 🔍 Verificar Instalações

```powershell
# Verificar Node.js
node --version

# Verificar npm
npm --version

# Verificar PHP (após instalar)
php --version

# Verificar Composer (após instalar)
composer --version
```
