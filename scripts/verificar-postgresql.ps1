# Script para verificar status do PostgreSQL

Write-Host "🔍 Verificando PostgreSQL..." -ForegroundColor Cyan
Write-Host ""

# 1. Verificar serviços
Write-Host "1. Serviços PostgreSQL:" -ForegroundColor Yellow
$services = Get-Service -Name "*postgres*" -ErrorAction SilentlyContinue

if ($null -ne $services) {
    foreach ($service in $services) {
        if ($service.Status -eq 'Running') {
            Write-Host "   ✅ $($service.Name) - Rodando" -ForegroundColor Green
        } else {
            Write-Host "   ❌ $($service.Name) - Parado" -ForegroundColor Red
            Write-Host "   💡 Execute: Start-Service '$($service.Name)'" -ForegroundColor Yellow
        }
    }
} else {
    Write-Host "   ❌ Nenhum serviço PostgreSQL encontrado" -ForegroundColor Red
    Write-Host "   💡 PostgreSQL pode não estar instalado" -ForegroundColor Yellow
    Write-Host "   📥 Download: https://www.postgresql.org/download/windows/" -ForegroundColor Cyan
}

Write-Host ""

# 2. Verificar porta
Write-Host "2. Porta 5432:" -ForegroundColor Yellow
$port = Get-NetTCPConnection -LocalPort 5432 -ErrorAction SilentlyContinue

if ($null -ne $port) {
    Write-Host "   ✅ Porta 5432 está em uso" -ForegroundColor Green
} else {
    Write-Host "   ❌ Porta 5432 está livre" -ForegroundColor Red
    Write-Host "   💡 PostgreSQL não está escutando" -ForegroundColor Yellow
}

Write-Host ""

# 3. Verificar extensão PHP
Write-Host "3. Extensão PHP pdo_pgsql:" -ForegroundColor Yellow
$extLoaded = php -r "echo extension_loaded('pdo_pgsql') ? '1' : '0';"
if ($extLoaded -eq '1') {
    Write-Host "   ✅ Habilitada" -ForegroundColor Green
} else {
    Write-Host "   ❌ Desabilitada" -ForegroundColor Red
}

Write-Host ""

# 4. Verificar .env
Write-Host "4. Configuração .env:" -ForegroundColor Yellow
if (Test-Path .env) {
    $envLines = Get-Content .env
    
    foreach ($line in $envLines) {
        if ($line -match '^DB_CONNECTION=') {
            Write-Host "   $line" -ForegroundColor Cyan
        }
        if ($line -match '^DB_USERNAME=') {
            $user = $line -replace 'DB_USERNAME=', ''
            Write-Host "   $line" -ForegroundColor Cyan
            if ($user -ne 'postgres') {
                Write-Host "   ⚠️  Username deve ser 'postgres'" -ForegroundColor Yellow
            }
        }
        if ($line -match '^DB_PASSWORD=') {
            $pass = $line -replace 'DB_PASSWORD=', ''
            if ($pass -eq '' -or $pass -match '^\s*$') {
                Write-Host "   ❌ DB_PASSWORD não configurado" -ForegroundColor Red
            } else {
                Write-Host "   ✅ DB_PASSWORD configurado" -ForegroundColor Green
            }
        }
    }
} else {
    Write-Host "   ❌ Arquivo .env não encontrado" -ForegroundColor Red
}

Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "📋 PRÓXIMOS PASSOS" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

if ($null -eq $services) {
    Write-Host "1. INSTALE PostgreSQL:" -ForegroundColor Yellow
    Write-Host "   https://www.postgresql.org/download/windows/" -ForegroundColor Cyan
    Write-Host "   Durante instalação, selecione também PostGIS" -ForegroundColor White
} elseif (($services | Where-Object { $_.Status -ne 'Running' })) {
    Write-Host "1. INICIE PostgreSQL:" -ForegroundColor Yellow
    Write-Host "   Win+R → services.msc → PostgreSQL → Iniciar" -ForegroundColor Cyan
}

if ($null -eq $port) {
    Write-Host "2. PostgreSQL precisa estar rodando na porta 5432" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "3. Após PostgreSQL rodando:" -ForegroundColor Yellow
Write-Host "   - Configure DB_PASSWORD no .env" -ForegroundColor White
Write-Host "   - Crie banco: CREATE DATABASE geoapp;" -ForegroundColor White
Write-Host "   - Execute: php artisan migrate" -ForegroundColor White
Write-Host ""