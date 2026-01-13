@echo off
echo 🔧 Configurando ambiente Docker...
echo.

REM Criar .env do backend se não existir
if not exist "semMultas\.env" (
    echo 📝 Criando .env do backend...
    (
        echo APP_NAME="Sem Multas"
        echo APP_ENV=local
        echo APP_KEY=
        echo APP_DEBUG=true
        echo APP_URL=http://localhost:8000
        echo.
        echo DB_CONNECTION=sqlite
        echo DB_DATABASE=/var/www/html/database/database.sqlite
        echo.
        echo JWT_SECRET=
    ) > semMultas\.env
    echo ✅ Arquivo semMultas\.env criado
) else (
    echo ℹ️  Arquivo semMultas\.env já existe
)

REM Criar .env do frontend se não existir
if not exist "semMultasWeb\.env" (
    echo 📝 Criando .env do frontend...
    echo VITE_API_URL=http://localhost:8000/api > semMultasWeb\.env
    echo ✅ Arquivo semMultasWeb\.env criado
) else (
    echo ℹ️  Arquivo semMultasWeb\.env já existe
)

REM Criar diretório de banco de dados
if not exist "semMultas\database" mkdir semMultas\database
if not exist "semMultas\database\database.sqlite" (
    type nul > semMultas\database\database.sqlite
)

echo.
echo ✅ Configuração concluída!
echo.
echo 🚀 Agora você pode executar:
echo    docker-start.bat        (desenvolvimento)
echo    docker-start.bat prod   (produção)
echo.
pause
