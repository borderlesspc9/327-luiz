@echo off
echo 🚀 Iniciando Sistema Sem Multas com Docker...
echo.

REM Verificar se Docker está rodando
docker info >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker não está rodando. Por favor, inicie o Docker Desktop.
    pause
    exit /b 1
)

REM Escolher modo
set MODE=%1
if "%MODE%"=="" set MODE=dev

if "%MODE%"=="prod" (
    echo 📦 Modo: PRODUÇÃO
    docker-compose -f docker-compose.yml up --build -d
) else (
    echo 🔧 Modo: DESENVOLVIMENTO
    docker-compose -f docker-compose.dev.yml up --build
)

echo.
echo ✅ Servidores iniciados!
echo.
echo 📍 Acessos:
echo    Frontend: http://localhost:5173
echo    Backend:  http://localhost:8000
echo    API:      http://localhost:8000/api
echo.
echo 💡 Para parar os containers: docker-compose down
echo 💡 Para ver logs: docker-compose logs -f
pause
