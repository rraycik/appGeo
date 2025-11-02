@echo off
echo Criando banco de dados geoapp...
echo.

cd "C:\Program Files\PostgreSQL\18\bin"

if exist "psql.exe" (
    echo Criando banco geoapp...
    psql.exe -U postgres -c "CREATE DATABASE geoapp;"
    
    if %errorlevel% == 0 (
        echo [OK] Banco criado com sucesso!
        echo.
        echo Habilitando extensao PostGIS...
        psql.exe -U postgres -d geoapp -c "CREATE EXTENSION IF NOT EXISTS postgis;"
        
        if %errorlevel% == 0 (
            echo [OK] PostGIS habilitado com sucesso!
            echo.
            echo Verificando...
            psql.exe -U postgres -d geoapp -c "SELECT PostGIS_Version();"
        ) else (
            echo [ERRO] Falha ao habilitar PostGIS
            echo Verifique se PostGIS foi instalado durante a instalacao do PostgreSQL
        )
    ) else (
        echo [ERRO] Falha ao criar banco
        echo Verifique se:
        echo   1. PostgreSQL esta rodando
        echo   2. A senha do usuario postgres esta correta
    )
) else (
    echo [ERRO] psql.exe nao encontrado!
)

echo.
pause
