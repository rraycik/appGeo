<?php

/**
 * Script de Verificação de Setup
 * Verifica se todos os requisitos estão configurados corretamente
 */

echo "🔍 Verificando configuração da aplicação GeoApp...\n\n";

$errors = [];
$warnings = [];
$success = [];

// Verificar versão PHP
echo "📦 PHP Version: ";
$phpVersion = PHP_VERSION;
echo $phpVersion . "\n";
if (version_compare($phpVersion, '8.4.0', '>=')) {
    $success[] = "PHP 8.4+ ✓";
} else {
    $warnings[] = "PHP 8.4+ recomendado (atual: $phpVersion)";
}

// Verificar extensões PHP
echo "\n🔌 Extensões PHP:\n";
$requiredExtensions = ['pdo_pgsql', 'intl', 'zip', 'gd', 'mbstring', 'xml'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "  - $ext: " . ($loaded ? "✓" : "✗") . "\n";
    if (!$loaded) {
        $errors[] = "Extensão PHP '$ext' não encontrada";
    }
}

// Verificar arquivo .env
echo "\n⚙️  Arquivo .env: ";
if (file_exists('.env')) {
    echo "✓\n";
    $success[] = "Arquivo .env existe";
    
    // Verificar configurações importantes
    $env = file_get_contents('.env');
    $checks = [
        'APP_KEY' => strpos($env, 'APP_KEY=') !== false && !strpos($env, 'APP_KEY=') === strpos($env, 'APP_KEY='),
        'DB_CONNECTION' => strpos($env, 'DB_CONNECTION=pgsql') !== false,
        'DB_DATABASE' => strpos($env, 'DB_DATABASE=geoapp') !== false,
    ];
    
    foreach ($checks as $key => $check) {
        if ($check) {
            echo "  ✓ $key configurado\n";
        } else {
            $warnings[] = "$key precisa ser configurado";
            echo "  ⚠ $key precisa ser verificado\n";
        }
    }
} else {
    echo "✗\n";
    $errors[] = "Arquivo .env não encontrado. Execute: cp .env.example .env";
}

// Verificar diretórios
echo "\n📁 Diretórios:\n";
$requiredDirs = [
    'storage/app/public/geojson' => true,
    'storage/framework/cache' => false,
    'storage/logs' => false,
    'bootstrap/cache' => false,
];
foreach ($requiredDirs as $dir => $mustExist) {
    $exists = is_dir($dir);
    echo "  - $dir: " . ($exists ? "✓" : "✗") . "\n";
    if ($mustExist && !$exists) {
        $warnings[] = "Diretório '$dir' deve existir";
    }
}

// Verificar migrations
echo "\n🗄️  Migrations:\n";
$migrationFiles = glob('database/migrations/*.php');
$hasLayersMigration = false;
foreach ($migrationFiles as $file) {
    if (strpos($file, 'create_layers_table') !== false) {
        $hasLayersMigration = true;
        echo "  ✓ Migration layers encontrada\n";
        break;
    }
}
if (!$hasLayersMigration) {
    $errors[] = "Migration create_layers_table não encontrada";
}

// Verificar Models
echo "\n📦 Models:\n";
if (file_exists('app/Models/Layer.php')) {
    echo "  ✓ Layer.php\n";
    $success[] = "Model Layer existe";
} else {
    $errors[] = "Model Layer.php não encontrado";
}

// Verificar Services
echo "\n🔧 Services:\n";
$services = ['LayerService.php'];
foreach ($services as $service) {
    $path = "app/Services/$service";
    if (file_exists($path)) {
        echo "  ✓ $service\n";
    } else {
        $errors[] = "Service $service não encontrado";
    }
}

// Verificar Repositories
echo "\n📚 Repositories:\n";
$repos = ['LayerRepository.php', 'LayerRepositoryInterface.php'];
foreach ($repos as $repo) {
    $path = "app/Repositories/$repo";
    if (file_exists($path)) {
        echo "  ✓ $repo\n";
    } else {
        $errors[] = "Repository $repo não encontrado";
    }
}

// Resumo
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RESUMO\n";
echo str_repeat("=", 60) . "\n";

if (count($success) > 0) {
    echo "\n✅ Sucessos (" . count($success) . "):\n";
    foreach ($success as $item) {
        echo "   - $item\n";
    }
}

if (count($warnings) > 0) {
    echo "\n⚠️  Avisos (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   - $item\n";
    }
}

if (count($errors) > 0) {
    echo "\n❌ Erros (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   - $item\n";
    }
    echo "\n⚠️  Por favor, corrija os erros antes de continuar.\n";
    exit(1);
} else {
    echo "\n✅ Todos os requisitos básicos estão configurados!\n";
    echo "\n📝 Próximos passos:\n";
    echo "   1. Configure o banco de dados PostgreSQL no .env\n";
    echo "   2. Execute: php artisan migrate\n";
    echo "   3. Execute: php artisan make:filament-user\n";
    echo "   4. Execute: php artisan storage:link\n";
    echo "   5. Inicie o servidor: php artisan serve\n";
    exit(0);
}
