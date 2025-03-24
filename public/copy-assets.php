<?php
/**
 * Script d'urgence pour copier physiquement les assets entre les répertoires.
 * À exécuter directement via HTTP: https://pivot.guillaume-lcte.fr/copy-assets.php
 */

header('Content-Type: text/plain');
echo "=== Script de copie physique des assets ===\n\n";

// Fonction pour répertorier tous les fichiers d'un répertoire et ses sous-répertoires
function listFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

// Fonction pour copier un fichier en créant les répertoires intermédiaires si nécessaire
function copyFileWithDirs($source, $destination) {
    $dirName = dirname($destination);
    if (!is_dir($dirName)) {
        mkdir($dirName, 0755, true);
    }
    return copy($source, $destination);
}

// Répertoires source et destination
$assetsDir = __DIR__ . '/assets';
$buildDir = __DIR__ . '/build/assets';

// Vérifier si les répertoires existent
if (!is_dir($assetsDir)) {
    echo "❌ Le répertoire assets n'existe pas. Exécutez fix-assets.php d'abord.\n";
    exit(1);
}

// Créer le répertoire build/assets s'il n'existe pas
if (!is_dir($buildDir)) {
    echo "Création du répertoire build/assets...\n";
    mkdir($buildDir, 0755, true);
    echo "✅ Répertoire build/assets créé.\n";
} else {
    echo "Le répertoire build/assets existe déjà.\n";
}

// Créer une structure miroir pour les hachages manquants
echo "Tentative de reconstruction des fichiers avec les hachages corrects...\n";

// Créer manuellement les liens pour les fichiers spécifiques manquants
$missingFiles = [
    'css/app-CjAB3oxN.css' => 'css/app.css',
    'js/vendor-CLLTD4I8.js' => 'js/vendor.js',
    'js/app-gZAm2HJZ.js' => 'js/app.js'
];

foreach ($missingFiles as $hashFile => $baseFile) {
    // Chemin source (fichier sans hachage)
    $source = $assetsDir . '/' . $baseFile;
    
    // Chemin destination (fichier avec hachage dans build/assets)
    $destination = $buildDir . '/' . $hashFile;
    
    if (file_exists($source)) {
        echo "Copie de {$source} vers {$destination}...\n";
        if (copyFileWithDirs($source, $destination)) {
            echo "✅ Fichier {$hashFile} créé avec succès.\n";
        } else {
            echo "❌ Erreur lors de la copie de {$hashFile}.\n";
        }
    } else {
        echo "⚠️ Fichier source {$source} introuvable, création d'un fichier vide...\n";
        // Créer un fichier minimal
        if ($hashFile === 'css/app-CjAB3oxN.css') {
            $content = "/* Fichier CSS généré par copy-assets.php */\n";
        } else {
            $content = "console.log('Fichier JS généré par copy-assets.php');";
        }
        
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }
        
        file_put_contents($destination, $content);
        echo "✅ Fichier vide {$hashFile} créé.\n";
    }
}

// Copier également tous les fichiers du répertoire assets vers build/assets
echo "\nCopie de tous les fichiers de assets vers build/assets...\n";
$assetsFiles = listFiles($assetsDir);
$count = 0;

foreach ($assetsFiles as $file) {
    $relativePath = str_replace($assetsDir . '/', '', $file);
    $destinationPath = $buildDir . '/' . $relativePath;
    
    if (!file_exists($destinationPath)) {
        echo "Copie de {$relativePath}...\n";
        if (copyFileWithDirs($file, $destinationPath)) {
            $count++;
        }
    }
}

echo "✅ {$count} fichiers copiés de assets vers build/assets.\n";

// Modifier le fichier .htaccess de build pour forcer le navigateur à recharger les assets
$htaccessPath = $buildDir . '/.htaccess';
$htaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Force no-cache for all assets during le debugging
    <IfModule mod_headers.c>
        Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
        Header set Pragma "no-cache"
        Header set Expires "0"
    </IfModule>
    
    # Redirect to assets directory if file not found
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /assets/$1 [L,QSA]
</IfModule>
EOT;

file_put_contents($htaccessPath, $htaccessContent);
echo "✅ Fichier .htaccess mis à jour pour désactiver le cache.\n";

// Créer un index.php qui renvoie au répertoire parent
$indexPath = $buildDir . '/index.php';
$indexContent = <<<EOT
<?php
header('Location: /');
exit;
EOT;

file_put_contents($indexPath, $indexContent);
echo "✅ Fichier index.php créé pour rediriger les requêtes directes.\n";

echo "\n✅ Opération terminée. Veuillez:";
echo "\n1. Effacer le cache de votre navigateur (Ctrl+F5 ou ⌘+Shift+R)";
echo "\n2. Recharger la page principale (https://pivot.guillaume-lcte.fr)\n";
echo "\nSi le problème persiste, essayez d'ouvrir la console navigateur et exécutez:";
echo "\nlocalStorage.clear(); sessionStorage.clear(); location.reload(true);\n"; 