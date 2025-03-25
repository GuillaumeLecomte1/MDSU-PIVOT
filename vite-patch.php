<?php

/**
 * This script patches the Laravel Vite class to handle missing 'src' field in manifest.json
 * Since newer versions of Vite don't include the 'src' field which Laravel expects
 */

$vitePhpPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Vite.php';

if (!file_exists($vitePhpPath)) {
    echo "❌ Could not find Vite.php at expected path: {$vitePhpPath}\n";
    exit(1);
}

echo "🔍 Found Vite.php, checking for patch need...\n";

$vitePhpContents = file_get_contents($vitePhpPath);

// Check if already patched
if (strpos($vitePhpContents, 'isset($chunk[\'src\'])') !== false) {
    echo "✅ Vite.php is already patched\n";
    exit(0);
}

// Create backup
copy($vitePhpPath, $vitePhpPath . '.backup');
echo "📦 Created backup at {$vitePhpPath}.backup\n";

// Apply the patch
$search = '$path = $chunk[\'src\'];';
$replace = 'if (isset($chunk[\'src\'])) { $path = $chunk[\'src\']; } else { $path = $file; }';

$patchedContents = str_replace($search, $replace, $vitePhpContents);

if ($patchedContents === $vitePhpContents) {
    echo "⚠️ Unable to find the line to patch. The Vite.php file may have changed structure.\n";
    exit(1);
}

file_put_contents($vitePhpPath, $patchedContents);
echo "✅ Successfully patched Vite.php to handle missing 'src' field\n";

/**
 * Additionally, check the manifest.json structure and fix if needed
 */
$manifestPath = __DIR__ . '/public/build/manifest.json';

if (!file_exists($manifestPath)) {
    echo "❌ manifest.json not found at: {$manifestPath}\n";
    exit(1);
}

$manifest = json_decode(file_get_contents($manifestPath), true);

if (!$manifest) {
    echo "❌ Failed to parse manifest.json\n";
    exit(1);
}

$needsFix = false;

foreach ($manifest as $entry => $chunk) {
    if (!isset($chunk['src'])) {
        $needsFix = true;
        break;
    }
}

if ($needsFix) {
    echo "⚠️ Manifest.json is missing 'src' fields. Fixing...\n";
    
    // Backup original manifest
    copy($manifestPath, $manifestPath . '.backup');
    
    // Add src field to each entry
    foreach ($manifest as $entry => &$chunk) {
        if (!isset($chunk['src'])) {
            $chunk['src'] = $entry;
        }
    }
    
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "✅ Fixed manifest.json by adding missing 'src' fields\n";
}

echo "✅ Vite integration is now properly patched\n"; 