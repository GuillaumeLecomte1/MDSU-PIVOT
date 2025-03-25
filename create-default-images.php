<?php
/**
 * Script pour créer les images par défaut en local
 * Ce script va créer les images par défaut dans le dossier public/images/default
 */

// Définir les chemins
$publicPath = __DIR__ . '/public';
$defaultImagesDir = $publicPath . '/images/default';
$storagePath = __DIR__ . '/storage';
$storagePublicPath = $storagePath . '/app/public';

// Message d'information
echo "Création des images par défaut pour le développement local\n";

// Créer les répertoires nécessaires
if (!is_dir($defaultImagesDir)) {
    echo "Création du répertoire: $defaultImagesDir\n";
    mkdir($defaultImagesDir, 0755, true);
} else {
    echo "Le répertoire existe déjà: $defaultImagesDir\n";
}

// Créer le répertoire de stockage public
if (!is_dir($storagePublicPath)) {
    echo "Création du répertoire: $storagePublicPath\n";
    mkdir($storagePublicPath, 0755, true);
} else {
    echo "Le répertoire existe déjà: $storagePublicPath\n";
}

// Vérifier si le lien symbolique storage existe
if (!file_exists($publicPath . '/storage')) {
    echo "Création du lien symbolique storage\n";
    
    // Sous Windows, on a besoin d'utiliser l'artisan pour créer le lien
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        echo "Système Windows détecté, utilisation de la commande artisan\n";
        shell_exec('php artisan storage:link');
    } else {
        // Sous Unix, on peut créer le lien directement
        symlink($storagePublicPath, $publicPath . '/storage');
    }
} else {
    echo "Le lien symbolique storage existe déjà\n";
}

// Fonction pour créer une image PNG noire avec du texte
function createBlackImage($path, $text = 'Image non disponible', $width = 500, $height = 500) {
    // Créer une image
    $image = imagecreatetruecolor($width, $height);
    
    // Allouer les couleurs
    $black = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);
    
    // Remplir l'image en noir
    imagefill($image, 0, 0, $black);
    
    // Écrire le texte en blanc au centre
    $font = 5; // Police par défaut (taille 5)
    $text_width = imagefontwidth($font) * strlen($text);
    $text_height = imagefontheight($font);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font, $x, $y, $text, $white);
    
    // Sauvegarder l'image au format PNG
    imagepng($image, $path);
    
    // Libérer la mémoire
    imagedestroy($image);
    
    echo "Image créée: $path\n";
}

// Créer le favicon
$faviconPath = $publicPath . '/favicon.ico';
if (!file_exists($faviconPath)) {
    echo "Création du favicon: $faviconPath\n";
    // Créer un favicon simple (16x16 pixels)
    createBlackImage($defaultImagesDir . '/favicon.png', "", 16, 16);
    copy($defaultImagesDir . '/favicon.png', $faviconPath);
} else {
    echo "Le favicon existe déjà: $faviconPath\n";
}

// Créer l'image placeholder
createBlackImage($defaultImagesDir . '/placeholder.png', 'Image non disponible');

// Créer les images par type
$types = ['product', 'category', 'user', 'banner', 'logo', 'thumbnail'];
foreach ($types as $type) {
    createBlackImage($defaultImagesDir . "/$type.png", $type);
}

echo "Création des images par défaut terminée!\n";
echo "Les images sont disponibles dans: $defaultImagesDir\n";
echo "Le favicon est disponible à: $faviconPath\n"; 