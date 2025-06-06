<?php
/**
 * Script pour créer un manifest.json compatible avec Vite et générer des assets statiques
 * en fonction des chemins spécifiques demandés par l'application.
 */

header('Content-Type: text/plain');
echo "=== Script de génération de manifest.json et d'assets statiques ===\n\n";

// Liste des fichiers demandés dans les erreurs 404
$requestedFiles = [
    'build/assets/css/app-CjAB3oxN.css',
    'build/assets/js/vendor-CLLTD4I8.js',
    'build/assets/js/app-gZAm2HJZ.js'
];

// Extraction des informations de chemins
$fileInfo = [];
foreach ($requestedFiles as $file) {
    // Enlever le préfixe "build/assets/"
    $path = str_replace('build/assets/', '', $file);
    
    // Identifier le type et le nom du fichier
    if (preg_match('/^(js|css)\/([a-zA-Z0-9_-]+)-([a-zA-Z0-9]+)\.(js|css)$/', $path, $matches)) {
        $type = $matches[1];     // js ou css
        $name = $matches[2];     // nom du fichier (ex: app, vendor)
        $hash = $matches[3];     // hash (ex: CjAB3oxN)
        $ext = $matches[4];      // extension (js ou css)
        
        $fileInfo[] = [
            'path' => $path,
            'name' => $name,
            'type' => $type,
            'hash' => $hash,
            'ext' => $ext,
            'original' => "resources/{$type}/{$name}.{$ext}"
        ];
    }
}

// Création du contenu du manifest.json
$manifest = [];
foreach ($fileInfo as $info) {
    $manifest[$info['original']] = [
        'file' => $info['path'],
        'isEntry' => ($info['name'] === 'app'),
        'src' => $info['original']
    ];
}

// Créer les répertoires nécessaires
$buildDir = __DIR__ . '/build';
$assetsDir = __DIR__ . '/assets';
$buildAssetsDir = $buildDir . '/assets';

// Créer le répertoire build s'il n'existe pas
if (!is_dir($buildDir)) {
    echo "Création du répertoire build...\n";
    mkdir($buildDir, 0755, true);
}

// Créer le répertoire assets s'il n'existe pas
if (!is_dir($assetsDir)) {
    echo "Création du répertoire assets...\n";
    mkdir($assetsDir, 0755, true);
}

// Créer le répertoire build/assets s'il n'existe pas
if (!is_dir($buildAssetsDir)) {
    echo "Création du répertoire build/assets...\n";
    mkdir($buildAssetsDir, 0755, true);
}

// Fonction pour créer un répertoire si nécessaire
function createDirIfNotExists($dir) {
    if (!is_dir($dir)) {
        echo "Création du répertoire {$dir}...\n";
        mkdir($dir, 0755, true);
        return true;
    }
    return false;
}

// Créer les sous-répertoires nécessaires
createDirIfNotExists($buildAssetsDir . '/js');
createDirIfNotExists($buildAssetsDir . '/css');
createDirIfNotExists($assetsDir . '/js');
createDirIfNotExists($assetsDir . '/css');

// Enregistrer les manifests
file_put_contents($buildDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
echo "✅ Fichier manifest.json créé dans {$buildDir}/manifest.json\n";

file_put_contents($assetsDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
echo "✅ Fichier manifest.json créé dans {$assetsDir}/manifest.json\n";

// Créer les fichiers demandés avec un contenu minimal
foreach ($fileInfo as $info) {
    $buildFilePath = $buildAssetsDir . '/' . $info['path'];
    $assetsFilePath = $assetsDir . '/' . $info['path'];
    
    // Créer le contenu minimal en fonction du type de fichier
    if ($info['ext'] === 'js') {
        if ($info['name'] === 'vendor') {
            $content = "console.log('Vendor JS bundle generated by build-assets.php');";
        } else {
            $content = "console.log('Application JS bundle generated by build-assets.php');";
        }
    } else {
        $content = "/* CSS file generated by build-assets.php */\n";
        $content .= "body { font-family: sans-serif; }\n";
    }
    
    // Assurer que le répertoire parent existe
    createDirIfNotExists(dirname($buildFilePath));
    createDirIfNotExists(dirname($assetsFilePath));
    
    // Enregistrer les fichiers
    file_put_contents($buildFilePath, $content);
    echo "✅ Fichier créé: {$buildFilePath}\n";
    
    file_put_contents($assetsFilePath, $content);
    echo "✅ Fichier créé: {$assetsFilePath}\n";
    
    // Créer également des versions sans hash pour la compatibilité
    $noHashFile = preg_replace('/-[a-zA-Z0-9]+\./', '.', $info['path']);
    
    $buildNoHashPath = $buildAssetsDir . '/' . $noHashFile;
    file_put_contents($buildNoHashPath, $content);
    echo "✅ Fichier créé (sans hash): {$buildNoHashPath}\n";
    
    $assetsNoHashPath = $assetsDir . '/' . $noHashFile;
    file_put_contents($assetsNoHashPath, $content);
    echo "✅ Fichier créé (sans hash): {$assetsNoHashPath}\n";
}

// Créer un fichier .htaccess dans build/assets pour désactiver le cache
$htaccessContent = <<<EOT
<IfModule mod_headers.c>
    Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
    Header set Pragma "no-cache"
    Header set Expires "0"
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to assets directory if file not found
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /assets/$1 [L,QSA]
</IfModule>
EOT;

file_put_contents($buildAssetsDir . '/.htaccess', $htaccessContent);
echo "✅ Fichier .htaccess créé dans build/assets\n";

file_put_contents($assetsDir . '/.htaccess', $htaccessContent);
echo "✅ Fichier .htaccess créé dans assets\n";

echo "\n✅ Génération terminée!";
echo "\nVeuillez effacer le cache de votre navigateur (Ctrl+F5 ou Cmd+Shift+R) puis recharger la page."; 