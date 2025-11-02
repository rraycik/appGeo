<?php

/**
 * Script para diagnosticar e corrigir erro "could not find driver"
 */

echo "🔍 Diagnóstico de Extensões PHP\n";
echo str_repeat("=", 60) . "\n\n";

$extensions = [
    'pdo_pgsql' => 'PostgreSQL (Obrigatória)',
    'pdo' => 'PDO Base',
    'intl' => 'Internacionalização (Obrigatória)',
    'zip' => 'ZIP (Obrigatória)',
    'gd' => 'GD Image (Obrigatória)',
    'mbstring' => 'Multibyte String',
    'xml' => 'XML',
];

$errors = [];
$warnings = [];

foreach ($extensions as $ext => $description) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "✅ HABILITADA" : "❌ DESABILITADA";
    
    echo sprintf("%-15s %s - %s\n", $ext . ":", $status, $description);
    
    if (!$loaded) {
        if (in_array($ext, ['pdo_pgsql', 'intl', 'zip', 'gd'])) {
            $errors[] = $ext;
        } else {
            $warnings[] = $ext;
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

// Verificar php.ini
$iniPath = php_ini_loaded_file();
echo "\n📄 Arquivo php.ini:\n";
echo "   Caminho: " . ($iniPath ?: "Não encontrado") . "\n";

if ($iniPath && file_exists($iniPath)) {
    $iniContent = file_get_contents($iniPath);
    
    echo "\n🔍 Verificação no php.ini:\n";
    foreach (['pdo_pgsql', 'intl', 'zip', 'gd'] as $ext) {
        $commented = strpos($iniContent, ";extension=$ext") !== false;
        $enabled = strpos($iniContent, "extension=$ext") !== false && !$commented;
        
        if ($enabled) {
            echo "   ✅ extension=$ext (habilitado)\n";
        } elseif ($commented) {
            echo "   ⚠️  extension=$ext (comentado com ;)\n";
        } else {
            echo "   ❌ extension=$ext (não encontrado)\n";
        }
    }
}

// Verificar DLL
echo "\n📦 Verificação de DLLs:\n";
$extPath = ini_get('extension_dir');
if ($extPath) {
    echo "   Diretório de extensões: $extPath\n";
    
    foreach (['pdo_pgsql', 'intl', 'zip', 'gd'] as $ext) {
        $dll = $extPath . DIRECTORY_SEPARATOR . "php_$ext.dll";
        $exists = file_exists($dll);
        echo sprintf("   %s: %s\n", "php_$ext.dll", $exists ? "✅ Existe" : "❌ Não encontrado");
        
        if (!$exists && $ext === 'pdo_pgsql') {
            echo "      ⚠️  PostgreSQL pode não estar instalado\n";
        }
    }
} else {
    echo "   ⚠️  Diretório de extensões não configurado\n";
}

// Resumo
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RESUMO\n";
echo str_repeat("=", 60) . "\n";

if (count($errors) === 0) {
    echo "\n✅ Todas as extensões obrigatórias estão habilitadas!\n";
} else {
    echo "\n❌ Extensões faltando (" . count($errors) . "):\n";
    foreach ($errors as $ext) {
        echo "   - $ext\n";
    }
    
    echo "\n📝 SOLUÇÃO:\n";
    echo "   1. Abra o arquivo: " . ($iniPath ?: "php.ini") . "\n";
    echo "   2. Procure por: ;extension=pdo_pgsql\n";
    echo "   3. Remova o ; (ficando: extension=pdo_pgsql)\n";
    echo "   4. Repita para: intl, zip, gd\n";
    echo "   5. Reinicie o servidor Apache/Web\n";
    echo "\n   Para mais detalhes, veja: SOLUCAO_ERRO_DRIVER.md\n";
}

if (count($warnings) > 0) {
    echo "\n⚠️  Extensões recomendadas faltando:\n";
    foreach ($warnings as $ext) {
        echo "   - $ext\n";
    }
}

echo "\n";
