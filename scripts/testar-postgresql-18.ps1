# Testar PostgreSQL 18

$pgBin = "C:\Program Files\PostgreSQL\18\bin"

Write-Host "🔍 Testando PostgreSQL 18..." -ForegroundColor Cyan
Write-Host ""

# Verificar se psql existe
$psql = "$pgBin\psql.exe"
if (Test-Path $psql) {
    Write-Host "✅ psql.exe encontrado" -ForegroundColor Green
    
    # Tentar conectar
    Write-Host "Testando conexão..." -ForegroundColor Yellow
    & $psql -U postgres -c "SELECT version();" 2>&1
    
} else {
    Write-Host "❌ psql.exe não encontrado" -ForegroundColor Red
}

Write-Host ""

# Verificar porta
$port = Get-NetTCPConnection -LocalPort 5432 -ErrorAction SilentlyContinue
if ($port) {
    Write-Host "✅ Porta 5432 está em uso" -ForegroundColor Green
} else {
    Write-Host "❌ Porta 5432 não está em uso" -ForegroundColor Red
    Write-Host "   PostgreSQL precisa estar rodando" -ForegroundColor Yellow
}

Write-Host ""
