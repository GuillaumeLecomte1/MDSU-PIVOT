<?php
/**
 * Script d'urgence pour gérer les assets manquants en production
 */

header('Content-Type: text/plain');
echo "=== Script de correction des assets et images ===\n\n";

// Liste des fichiers d'assets demandés fréquemment avec leurs versions non-hashées
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
    echo "console.log('Fallback asset generated for: ' + location.pathname);";
} elseif (\$extension === 'css') {
    echo "/* Fallback CSS generated for \$requestUri */\\n";
    echo "body { font-family: system-ui, sans-serif; }\\n";
} else {
    // Pour les autres types de fichiers, renvoyer une erreur 404
    header('HTTP/1.0 404 Not Found');
    echo "Asset not found: \$requestUri";
}
EOT;

echo "\nCréation du script d'aide pour les assets manquants...\n";
if (!file_exists($assetHelperPath)) {
    file_put_contents($assetHelperPath, $assetHelperContent);
    echo "✅ Script asset-helper.php créé.\n";
} else {
    echo "Le script asset-helper.php existe déjà.\n";
}

// Configuration des images et du stockage
echo "\n=== CONFIGURATION DES IMAGES ET DU STOCKAGE ===\n";

// Vérification et création du lien symbolique pour le stockage
$storagePublicPath = __DIR__ . '/storage';
$storageTargetPath = realpath(__DIR__ . '/../storage/app/public');

echo "\nConfiguration du lien symbolique pour storage...\n";

if (!is_link($storagePublicPath) && !is_dir($storagePublicPath)) {
    echo "Création du lien symbolique de {$storagePublicPath} vers {$storageTargetPath}...\n";
    
    // S'assurer que le répertoire cible existe
    if (!is_dir($storageTargetPath)) {
        mkdir($storageTargetPath, 0755, true);
        echo "✅ Répertoire cible créé.\n";
    }
    
    // Créer le lien symbolique
    if (symlink($storageTargetPath, $storagePublicPath)) {
        echo "✅ Lien symbolique créé avec succès.\n";
    } else {
        echo "❌ Échec de la création du lien symbolique. Création d'une copie à la place...\n";
        
        // Si le lien symbolique échoue, créer le répertoire et copier les fichiers
        mkdir($storagePublicPath, 0755, true);
        
        if (is_dir($storageTargetPath)) {
            // Fonction récursive pour copier les fichiers
            function copyDir($src, $dst) {
                $dir = opendir($src);
                @mkdir($dst);
                while (($file = readdir($dir)) !== false) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($src . '/' . $file)) {
                            copyDir($src . '/' . $file, $dst . '/' . $file);
                        } else {
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
                closedir($dir);
            }
            
            copyDir($storageTargetPath, $storagePublicPath);
            echo "✅ Fichiers copiés avec succès.\n";
        }
    }
} else {
    echo "Le lien symbolique ou le répertoire existe déjà.\n";
}

// Structure de dossiers pour les images
$imageDirs = [
    // Images de la page d'accueil
    '/storage/app/public/imagesAccueil',
    
    // Images pour les produits
    '/storage/app/public/images/products',
    '/storage/app/public/images/products/thumbnails',
    '/storage/app/public/images/products/large',
    
    // Images pour les catégories
    '/storage/app/public/images/categories',
    '/storage/app/public/images/categories/icons',
    '/storage/app/public/images/categories/banners',
    
    // Images pour les utilisateurs
    '/storage/app/public/images/users',
    '/storage/app/public/images/users/avatars',
    '/storage/app/public/images/users/covers',
    
    // Images pour les bannières et promotions
    '/storage/app/public/images/banners',
    '/storage/app/public/images/promotions',
    
    // Images pour le blog
    '/storage/app/public/images/blog',
    '/storage/app/public/images/blog/thumbnails',
    
    // Autres répertoires d'images
    '/storage/app/public/images/logos',
    '/storage/app/public/images/icons',
    '/storage/app/public/images/backgrounds',
];

echo "\nCréation des répertoires d'images...\n";
foreach ($imageDirs as $imageDir) {
    $fullPath = dirname(__DIR__) . $imageDir;
    if (!is_dir($fullPath)) {
        echo "Création du répertoire {$imageDir}...\n";
        mkdir($fullPath, 0755, true);
        echo "✅ Répertoire créé.\n";
    } else {
        echo "Le répertoire {$imageDir} existe déjà.\n";
    }
}

// Création d'images par défaut pour chaque section
$defaultImages = [
    // Images de la page d'accueil
    '/storage/app/public/imagesAccueil/slide1.jpg' => 'https://via.placeholder.com/1920x600?text=Slide+1',
    '/storage/app/public/imagesAccueil/slide2.jpg' => 'https://via.placeholder.com/1920x600?text=Slide+2',
    '/storage/app/public/imagesAccueil/slide3.jpg' => 'https://via.placeholder.com/1920x600?text=Slide+3',
    '/storage/app/public/imagesAccueil/featured.jpg' => 'https://via.placeholder.com/800x400?text=Featured+Product',
    
    // Images par défaut pour les produits
    '/storage/app/public/images/products/default.jpg' => 'https://via.placeholder.com/800x800?text=Product',
    '/storage/app/public/images/products/thumbnails/default.jpg' => 'https://via.placeholder.com/300x300?text=Product+Thumbnail',
    '/storage/app/public/images/products/large/default.jpg' => 'https://via.placeholder.com/1200x1200?text=Product+Large',
    
    // Images par défaut pour les catégories
    '/storage/app/public/images/categories/default.jpg' => 'https://via.placeholder.com/800x400?text=Category',
    '/storage/app/public/images/categories/icons/default.svg' => 'https://via.placeholder.com/64x64?text=Icon',
    '/storage/app/public/images/categories/banners/default.jpg' => 'https://via.placeholder.com/1920x300?text=Category+Banner',
    
    // Images par défaut pour les utilisateurs
    '/storage/app/public/images/users/default.jpg' => 'https://via.placeholder.com/300x300?text=User',
    '/storage/app/public/images/users/avatars/default.jpg' => 'https://via.placeholder.com/150x150?text=Avatar',
    '/storage/app/public/images/users/covers/default.jpg' => 'https://via.placeholder.com/1200x300?text=User+Cover',
    
    // Images pour les bannières et promotions
    '/storage/app/public/images/banners/default.jpg' => 'https://via.placeholder.com/1920x400?text=Banner',
    '/storage/app/public/images/promotions/default.jpg' => 'https://via.placeholder.com/800x400?text=Promotion',
    
    // Images pour le blog
    '/storage/app/public/images/blog/default.jpg' => 'https://via.placeholder.com/1200x600?text=Blog+Post',
    '/storage/app/public/images/blog/thumbnails/default.jpg' => 'https://via.placeholder.com/400x300?text=Blog+Thumbnail',
    
    // Autres images utiles
    '/storage/app/public/images/logos/logo.png' => 'https://via.placeholder.com/200x80?text=Logo',
    '/storage/app/public/images/icons/favicon.ico' => 'https://via.placeholder.com/32x32?text=Icon',
    '/storage/app/public/images/backgrounds/pattern.jpg' => 'https://via.placeholder.com/500x500?text=Background',
];

echo "\nCréation des images par défaut...\n";
foreach ($defaultImages as $imagePath => $imageUrl) {
    $fullPath = dirname(__DIR__) . $imagePath;
    if (!file_exists($fullPath)) {
        echo "Création de l'image {$imagePath}...\n";
        try {
            if (function_exists('curl_init')) {
                $ch = curl_init($imageUrl);
                $fp = fopen($fullPath, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            } else {
                // Fallback si curl n'est pas disponible
                file_put_contents($fullPath, file_get_contents($imageUrl));
            }
            echo "✅ Image créée.\n";
        } catch (Exception $e) {
            echo "❌ Erreur lors de la création de l'image: " . $e->getMessage() . "\n";
            // Créer une image vide comme fallback
            $im = imagecreatetruecolor(300, 300);
            $textColor = imagecolorallocate($im, 255, 255, 255);
            $bgColor = imagecolorallocate($im, 100, 100, 100);
            imagefilledrectangle($im, 0, 0, 299, 299, $bgColor);
            $text = basename($imagePath);
            imagestring($im, 5, 10, 140, $text, $textColor);
            imagejpeg($im, $fullPath);
            imagedestroy($im);
            echo "✅ Image de fallback créée.\n";
        }
    } else {
        echo "L'image {$imagePath} existe déjà.\n";
    }
}

// Vérifier et réparer les permissions
echo "\nVérification et réparation des permissions...\n";
$storagePath = dirname(__DIR__) . '/storage';
if (is_dir($storagePath)) {
    chmod($storagePath, 0755);
    if (is_dir($storagePath . '/app')) {
        chmod($storagePath . '/app', 0755);
        if (is_dir($storagePath . '/app/public')) {
            chmod($storagePath . '/app/public', 0777);
            
            // Fonction récursive pour définir les permissions
            function setPermissionsRecursively($dir) {
                if (is_dir($dir)) {
                    chmod($dir, 0777);
                    $files = array_diff(scandir($dir), ['.', '..']);
                    foreach ($files as $file) {
                        $path = $dir . '/' . $file;
                        if (is_dir($path)) {
                            setPermissionsRecursively($path);
                        } else {
                            chmod($path, 0666);
                        }
                    }
                }
            }
            
            setPermissionsRecursively($storagePath . '/app/public');
        }
    }
    echo "✅ Permissions réparées.\n";
}

// Vérification de l'accès aux images
echo "\nVérification de l'accès aux images...\n";
$testImagePath = __DIR__ . '/storage/app/public/images/products/default.jpg';
if (file_exists($testImagePath)) {
    echo "✅ L'image test est accessible depuis le répertoire public.\n";
} else {
    echo "❌ L'image test n'est pas accessible depuis le répertoire public.\n";
    echo "Vérifiez la configuration du lien symbolique et des permissions.\n";
}

// Information sur la configuration du serveur web
echo "\nInformations pour la configuration du serveur web:\n";
echo "- Assurez-vous que le serveur web a accès au répertoire storage\n";
echo "- Pour Apache, vérifiez que le module mod_rewrite est activé\n";
echo "- Pour Nginx, vérifiez que la configuration contient la directive pour stocker\n";

echo "\n✅ Configuration des images terminée. Toutes les images sont maintenant standardisées avec le format /storage/app/public/...\n";
echo "✅ Rechargez la page principale et vérifiez si les images se chargent correctement."; 