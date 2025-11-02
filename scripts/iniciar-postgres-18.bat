@echo off
echo Iniciando PostgreSQL 18...
echo.

cd "C:\Program Files\PostgreSQL\18\bin"

if exist "pg_ctl.exe" (
    echo PostgreSQL encontrado!
    echo.
    echo Iniciando servidor...
    pg_ctl.exe start -D "C:\Program Files\PostgreSQL\18\data"
    echo.
    echo Aguardando 3 segundos...
    timeout /t 3 /nobreak >nul
    echo.
    
    netstat -an | findstr ":5432" >nul
    if %errorlevel% == 0 (
        echo [OK] PostgreSQL esta rodando na porta 5432!
    ) else (
        echo [ERRO] PostgreSQL nao esta respondendo na porta 5432
        echo Verifique se ha algum problema na instalacao
    )
) else (
    echo [ERRO] pg_ctl.exe nao encontrado!
    echo Verifique se PostgreSQL 18 esta instalado corretamente
)

echo.
pause
