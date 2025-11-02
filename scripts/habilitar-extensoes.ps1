# Script PowerShell para habilitar extensões PHP automaticamente
# Execute como Administrador

$phpIni = "C:\xampp\php\php.ini"

Write-Host "🔧 Habilitando extensões PHP..." -ForegroundColor Cyan
Write-Host ""

if (-not (Test-Path $phpIni)) {
    Write-Host "❌ Arquivo php.ini não encontrado em: $phpIni" -ForegroundColor Red
    Write-Host "   Por favor, verifique o caminho do PHP." -ForegroundColor Yellow
    exit 1
}

Write-Host "📄 Arquivo encontrado: $phpIni" -ForegroundColor Green
Write-Host ""

# Ler conteúdo do php.ini
$content = Get-Content $phpIni -Raw
$originalContent = $content

# Extensões para habilitar
$extensions = @(
    'pdo_pgsql',
    'intl',
    'zip',
    'gd'
)

$changes = 0

foreach ($ext in $extensions) {
    # Padrão 1: ;extension=nome
    $pattern1 = ";extension=$ext"
    $replacement1 = "extension=$ext"
    
    # Padrão 2: ; extension=nome (com espaço)
    $pattern2 = "; extension=$ext"
    $replacement2 = "extension=$ext"
    
    # Padrão 3: ;extension=nome.dll (com .dll)
    $pattern3 = ";extension=$ext\.dll"
    $replacement3 = "extension=$ext.dll"
    
    $before = $content
    
    # Fazer as substituições
    $content = $content -replace [regex]::Escape($pattern1), $replacement1
    $content = $content -replace [regex]::Escape($pattern2), $replacement2
    $content = $content -replace $pattern3, $replacement3
    
    if ($content -ne $before) {
        Write-Host "✅ Habilitada: extension=$ext" -ForegroundColor Green
        $changes++
    } else {
        # Verificar se já está habilitado
        if ($content -match "^extension=$ext" -or $content -match "^extension=$ext\.dll") {
            Write-Host "ℹ️  Já habilitada: extension=$ext" -ForegroundColor Yellow
        } else {
            Write-Host "⚠️  Não encontrado: extension=$ext" -ForegroundColor Yellow
            Write-Host "   Adicionando ao final do arquivo..." -ForegroundColor Cyan
            
            # Adicionar no final do arquivo
            $content += "`n; Habilitado automaticamente`nextension=$ext`n"
            $changes++
        }
    }
}

if ($changes -gt 0) {
    try {
        # Fazer backup
        $backup = "$phpIni.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
        Copy-Item $phpIni $backup
        Write-Host ""
        Write-Host "💾 Backup criado: $backup" -ForegroundColor Cyan
        
        # Salvar alterações
        Set-Content -Path $phpIni -Value $content -NoNewline
        
        Write-Host ""
        Write-Host "✅ php.ini atualizado com sucesso!" -ForegroundColor Green
        Write-Host ""
        Write-Host "⚠️  IMPORTANTE: Reinicie o Apache/servidor web!" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "No XAMPP Control Panel:" -ForegroundColor Cyan
        Write-Host "   1. Pare o Apache (Stop)" -ForegroundColor White
        Write-Host "   2. Inicie novamente (Start)" -ForegroundColor White
        Write-Host ""
        Write-Host "Após reiniciar, execute: php scripts/fix-driver-error.php" -ForegroundColor Cyan
    }
    catch {
        Write-Host ""
        Write-Host "❌ Erro ao salvar php.ini: $_" -ForegroundColor Red
        Write-Host "   Você pode precisar executar como Administrador." -ForegroundColor Yellow
        exit 1
    }
} else {
    Write-Host ""
    Write-Host "ℹ️  Nenhuma alteração necessária. Extensões já estão configuradas." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "📋 Verificação:" -ForegroundColor Cyan
php -r "echo 'pdo_pgsql: ' . (extension_loaded('pdo_pgsql') ? 'OK' : 'FALTA') . PHP_EOL;"
php -r "echo 'intl: ' . (extension_loaded('intl') ? 'OK' : 'FALTA') . PHP_EOL;"
php -r "echo 'zip: ' . (extension_loaded('zip') ? 'OK' : 'FALTA') . PHP_EOL;"
php -r "echo 'gd: ' . (extension_loaded('gd') ? 'OK' : 'FALTA') . PHP_EOL;"
Write-Host ""
Write-Host "⚠️  Nota: As extensões só estarão disponíveis após reiniciar o servidor." -ForegroundColor Yellow
