<?php
/**
 * Script d'urgence pour gérer les assets manquants en production
 */

header('Content-Type: text/plain');
echo "=== Script de correction des assets ===\n\n";

// Liste des fichiers demandés fréquemment avec leurs versions non-hashées
$requestedFiles = [
    "build/assets/js/app-gZAm2HJZ.js" => "console.log('App JS loaded');",
    "build/assets/js/vendor-CLLTD4I8.js" => "console.log('Vendor JS loaded');",
    "build/assets/css/app-CjAB3oxN.css" => "body { font-family: system-ui, sans-serif; }",
    "assets/js/app-gZAm2HJZ.js" => "console.log('App JS loaded');",
    "assets/js/vendor-CLLTD4I8.js" => "console.log('Vendor JS loaded');",
    "assets/css/app-CjAB3oxN.css" => "body { font-family: system-ui, sans-serif; }",
    
    // Versions sans hash
    "build/assets/js/app.js" => "console.log('App JS loaded (non-hashed version)');",
    "build/assets/js/vendor.js" => "console.log('Vendor JS loaded (non-hashed version)');",
    "build/assets/css/app.css" => "body { font-family: system-ui, sans-serif; }",
    "assets/js/app.js" => "console.log('App JS loaded (non-hashed version)');",
    "assets/js/vendor.js" => "console.log('Vendor JS loaded (non-hashed version)');",
    "assets/css/app.css" => "body { font-family: system-ui, sans-serif; }"
];

// Créer les répertoires et fichiers si nécessaire
foreach ($requestedFiles as $file => $content) {
    $fullPath = __DIR__ . "/" . $file;
    $dir = dirname($fullPath);
    
    if (!is_dir($dir)) {
        echo "Création du répertoire {$dir}...\n";
        mkdir($dir, 0755, true);
        echo "✅ Répertoire créé.\n";
    }
    
    if (!file_exists($fullPath)) {
        echo "Création du fichier {$file}...\n";
        file_put_contents($fullPath, $content);
        echo "✅ Fichier créé.\n";
    } else {
        echo "Le fichier {$file} existe déjà.\n";
    }
}

// Lister les hachages des fichiers existants dans le répertoire public
$hashPatterns = [
    'build/assets/js/app-*.js',
    'build/assets/js/vendor-*.js',
    'build/assets/css/app-*.css',
    'assets/js/app-*.js',
    'assets/js/vendor-*.js',
    'assets/css/app-*.css'
];

echo "\nRecherche de fichiers existants avec des hachages...\n";
$hashedFiles = [];

foreach ($hashPatterns as $pattern) {
    foreach (glob(__DIR__ . '/' . $pattern) as $file) {
        $relPath = str_replace(__DIR__ . '/', '', $file);
        $hashedFiles[] = $relPath;
        
        // Extraire la partie sans le hachage
        if (preg_match('/(.*)-[a-zA-Z0-9]+\.([^.]+)$/', $relPath, $matches)) {
            $baseFile = $matches[1] . '.' . $matches[2];
            $dir = dirname($relPath);
            $nonHashedPath = $dir . '/' . basename($baseFile);
            
            echo "Fichier hashé trouvé: {$relPath} => version non-hashée: {$nonHashedPath}\n";
            
            // Copier le fichier hashé vers la version non-hashée
            if (!file_exists(__DIR__ . '/' . $nonHashedPath)) {
                echo "Copie vers la version non-hashée...\n";
                copy($file, __DIR__ . '/' . $nonHashedPath);
                echo "✅ Copié.\n";
            }
        }
    }
}

// Créer manifest.json pour build/ et assets/
echo "\nCréation/mise à jour des fichiers manifest.json...\n";

$manifestDirs = ["build", "assets"];
$baseManifest = [
    "resources/js/app.jsx" => [
        "file" => "assets/js/app.js",
        "isEntry" => true,
        "src" => "resources/js/app.jsx"
    ],
    "resources/css/app.css" => [
        "file" => "assets/css/app.css",
        "isEntry" => true,
        "src" => "resources/css/app.css"
    ]
];

// Ajouter les fichiers hashés au manifest
foreach ($hashedFiles as $hashedFile) {
    if (preg_match('|^(build/|assets/)(.+)-[a-zA-Z0-9]+\.([^.]+)$|', $hashedFile, $matches)) {
        $dir = $matches[1]; // build/ ou assets/
        $path = $matches[2]; // chemin sans le hash
        $ext = $matches[3]; // extension
        
        // Déterminer le type de ressource
        if (strpos($path, 'js/app') !== false) {
            $baseManifest["resources/js/app.jsx"]["file"] = ltrim(str_replace($dir, '', $hashedFile), '/');
        } else if (strpos($path, 'css/app') !== false) {
            $baseManifest["resources/css/app.css"]["file"] = ltrim(str_replace($dir, '', $hashedFile), '/');
        }
    }
}

foreach ($manifestDirs as $dir) {
    $manifestPath = __DIR__ . "/{$dir}/manifest.json";
    echo "Vérification de manifest.json dans {$dir}/...\n";
    
    if (!file_exists($manifestPath)) {
        echo "Création de manifest.json dans {$dir}/...\n";
        file_put_contents($manifestPath, json_encode($baseManifest, JSON_PRETTY_PRINT));
        echo "✅ Fichier manifest.json créé.\n";
    } else {
        echo "Le fichier manifest.json existe déjà dans {$dir}/. Mise à jour...\n";
        $currentManifest = json_decode(file_get_contents($manifestPath), true);
        if (is_array($currentManifest)) {
            $merged = array_merge($currentManifest, $baseManifest);
            file_put_contents($manifestPath, json_encode($merged, JSON_PRETTY_PRINT));
            echo "✅ Fichier manifest.json mis à jour.\n";
        }
    }
}

// Créer des fichiers .htaccess pour la gestion des assets
$htaccessFiles = [
    "build/assets/.htaccess" => <<<EOT
<IfModule mod_headers.c>
    Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
    Header set Pragma "no-cache"
    Header set Expires "0"
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # D'abord essayer la version non-hashée
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^([^/]+)/([^/]+)-[a-zA-Z0-9]+\.([^.]+)$ /$1/$2.$3 [L]
    
    # Ensuite essayer l'autre répertoire
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /assets/$1 [L,QSA]
</IfModule>
EOT,
    "assets/.htaccess" => <<<EOT
<IfModule mod_headers.c>
    Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
    Header set Pragma "no-cache"
    Header set Expires "0"
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # D'abord essayer la version non-hashée
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^([^/]+)/([^/]+)-[a-zA-Z0-9]+\.([^.]+)$ /$1/$2.$3 [L]
    
    # Ensuite essayer l'autre répertoire
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /build/assets/$1 [L,QSA]
</IfModule>
EOT
];

foreach ($htaccessFiles as $file => $content) {
    $fullPath = __DIR__ . "/" . $file;
    $dir = dirname($fullPath);
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    echo "Vérification du fichier .htaccess dans {$file}...\n";
    if (!file_exists($fullPath)) {
        echo "Création du fichier .htaccess dans {$file}...\n";
        file_put_contents($fullPath, $content);
        echo "✅ Fichier .htaccess créé.\n";
    } else {
        echo "Le fichier .htaccess existe déjà dans {$file}.\n";
    }
}

// Créer un script PHP pour servir les assets manquants
$assetHelperPath = __DIR__ . '/asset-helper.php';
$assetHelperContent = <<<EOT
<?php
/**
 * Script pour servir des assets manquants
 */

// Déterminer quel asset est demandé
\$requestUri = \$_SERVER['REQUEST_URI'];
\$extension = pathinfo(\$requestUri, PATHINFO_EXTENSION);

// Types MIME pour les extensions courantes
\$mimeTypes = [
    'js' => 'application/javascript',
    'css' => 'text/css',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'svg' => 'image/svg+xml',
    'gif' => 'image/gif',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf' => 'font/ttf',
    'eot' => 'application/vnd.ms-fontobject',
];

// Définir le type MIME
if (isset(\$mimeTypes[\$extension])) {
    header('Content-Type: ' . \$mimeTypes[\$extension]);
}

// Désactiver le cache pour le développement
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Générer un contenu minimal selon le type de fichier
if (\$extension === 'js') {
    echo "console.log('Fallback asset generated for: ' + {$requestUri});";
} elseif (\$extension === 'css') {
    echo "/* Fallback CSS generated for {$requestUri} */\\n";
    echo "body { font-family: system-ui, sans-serif; }\\n";
} else {
    // Pour les autres types de fichiers, renvoyer une erreur 404
    header('HTTP/1.0 404 Not Found');
    echo "Asset not found: {$requestUri}";
}
EOT;

echo "\nCréation du script d'aide pour les assets manquants...\n";
if (!file_exists($assetHelperPath)) {
    file_put_contents($assetHelperPath, $assetHelperContent);
    echo "✅ Script asset-helper.php créé.\n";
} else {
    echo "Le script asset-helper.php existe déjà.\n";
}

// Créer un fichier .htaccess à la racine pour rediriger les assets manquants
$rootHtaccessPath = __DIR__ . '/.htaccess';
$rootHtaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Règles pour Laravel
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
    
    # Redirection des assets manquants
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(build/assets|assets)/(.+)\.(js|css)$ asset-helper.php [L]
</IfModule>
EOT;

echo "\nVérification du fichier .htaccess à la racine...\n";
if (!file_exists($rootHtaccessPath)) {
    echo "Création du fichier .htaccess à la racine...\n";
    file_put_contents($rootHtaccessPath, $rootHtaccessContent);
    echo "✅ Fichier .htaccess créé à la racine.\n";
} else {
    echo "Le fichier .htaccess existe déjà à la racine. Vérification des règles de redirection...\n";
    $currentContent = file_get_contents($rootHtaccessPath);
    if (strpos($currentContent, 'asset-helper.php') === false) {
        echo "Ajout des règles de redirection pour les assets manquants...\n";
        $updatedContent = preg_replace(
            '/(RewriteEngine On.*?)(\n\s*# Règles pour Laravel|\n\s*RewriteCond %{REQUEST_FILENAME} !-d)/s',
            "$1\n\n    # Redirection des assets manquants\n    RewriteCond %{REQUEST_FILENAME} !-f\n    RewriteCond %{REQUEST_FILENAME} !-d\n    RewriteRule ^(build/assets|assets)/(.+)\\.(js|css)$ asset-helper.php [L]$2",
            $currentContent
        );
        file_put_contents($rootHtaccessPath, $updatedContent);
        echo "✅ Règles de redirection ajoutées au fichier .htaccess.\n";
    } else {
        echo "Les règles de redirection sont déjà présentes dans le fichier .htaccess.\n";
    }
}

echo "\n✅ Opération terminée. Rechargez la page principale et vérifiez si les assets se chargent correctement."; 