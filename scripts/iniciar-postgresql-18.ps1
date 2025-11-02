# Script para iniciar PostgreSQL 18

$pgPath = "C:\Program Files\PostgreSQL\18"

Write-Host "🔍 Procurando serviço PostgreSQL 18..." -ForegroundColor Cyan
Write-Host ""

# Tentar encontrar serviço
$services = Get-Service | Where-Object { 
    $_.Name -like "*postgres*" -or 
    $_.DisplayName -like "*PostgreSQL*18*" -or
    $_.DisplayName -like "*postgresql*18*"
}

if ($services) {
    Write-Host "✅ Serviço encontrado:" -ForegroundColor Green
    foreach ($svc in $services) {
        Write-Host "   Nome: $($svc.Name)" -ForegroundColor Cyan
        Write-Host "   Display: $($svc.DisplayName)" -ForegroundColor Cyan
        Write-Host "   Status: $($svc.Status)" -ForegroundColor $(if ($svc.Status -eq 'Running') {'Green'} else {'Red'})
        
        if ($svc.Status -ne 'Running') {
            Write-Host ""
            Write-Host "🚀 Iniciando serviço..." -ForegroundColor Yellow
            try {
                Start-Service -Name $svc.Name
                Write-Host "✅ Serviço iniciado com sucesso!" -ForegroundColor Green
            } catch {
                Write-Host "❌ Erro ao iniciar: $_" -ForegroundColor Red
                Write-Host "💡 Execute como Administrador!" -ForegroundColor Yellow
            }
        } else {
            Write-Host "✅ Serviço já está rodando!" -ForegroundColor Green
        }
    }
} else {
    Write-Host "⚠️  Serviço não encontrado automaticamente" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Tentando iniciar via pg_ctl..." -ForegroundColor Cyan
    
    $pgCtl = "$pgPath\bin\pg_ctl.exe"
    $dataDir = "$pgPath\data"
    
    if (Test-Path $pgCtl) {
        if (Test-Path $dataDir) {
            Write-Host "Iniciando PostgreSQL..." -ForegroundColor Yellow
            & $pgCtl start -D $dataDir
        } else {
            Write-Host "❌ Diretório de dados não encontrado: $dataDir" -ForegroundColor Red
        }
    } else {
        Write-Host "❌ pg_ctl.exe não encontrado em: $pgCtl" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Verificando porta 5432..." -ForegroundColor Cyan
Start-Sleep -Seconds 2
$port = Get-NetTCPConnection -LocalPort 5432 -ErrorAction SilentlyContinue
if ($port) {
    Write-Host "✅ PostgreSQL está rodando na porta 5432!" -ForegroundColor Green
} else {
    Write-Host "❌ Porta 5432 ainda não está em uso" -ForegroundColor Red
    Write-Host ""
    Write-Host "💡 Tente:" -ForegroundColor Yellow
    Write-Host "   1. Abrir Services (Win+R → services.msc)" -ForegroundColor White
    Write-Host "   2. Procurar 'PostgreSQL' ou 'postgresql'" -ForegroundColor White
    Write-Host "   3. Iniciar o serviço manualmente" -ForegroundColor White
}

Write-Host ""
