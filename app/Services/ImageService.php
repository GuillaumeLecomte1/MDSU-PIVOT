<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ImageService
{
    private const ALLOWED_FORMATS = ['jpg', 'jpeg', 'png', 'webp'];
    private const DEFAULT_QUALITY = 80;
    private const DEFAULT_FORMAT = 'webp';
    private const IMAGE_SIZES = [
        'thumb' => ['width' => 150, 'height' => 150],
        'medium' => ['width' => 300, 'height' => 300],
        'large' => ['width' => 800, 'height' => 800],
    ];

    /**
     * Chemin de base pour le stockage des images publiques
     */
    protected string $basePath = 'app/public/';

    /**
     * Répertoires d'images disponibles
     */
    protected array $availableDirs = [
        'products' => 'images/products/',
        'product_thumbnails' => 'images/products/thumbnails/',
        'product_large' => 'images/products/large/',
        'categories' => 'images/categories/',
        'category_icons' => 'images/categories/icons/',
        'category_banners' => 'images/categories/banners/',
        'users' => 'images/users/',
        'user_avatars' => 'images/users/avatars/',
        'user_covers' => 'images/users/covers/',
        'banners' => 'images/banners/',
        'promotions' => 'images/promotions/',
        'blog' => 'images/blog/',
        'blog_thumbnails' => 'images/blog/thumbnails/',
        'logos' => 'images/logos/',
        'icons' => 'images/icons/',
        'backgrounds' => 'images/backgrounds/',
        'accueil' => 'imagesAccueil/',
    ];

    /**
     * Retourne le chemin complet pour stocker une image
     */
    public function getStoragePath(string $directory): string
    {
        if (!isset($this->availableDirs[$directory])) {
            throw new \InvalidArgumentException("Le répertoire {$directory} n'est pas valide");
        }

        return $this->basePath . $this->availableDirs[$directory];
    }

    /**
     * Retourne l'URL publique pour une image
     */
    public function getImageUrl(string $directory, string $filename): string
    {
        if (!isset($this->availableDirs[$directory])) {
            throw new \InvalidArgumentException("Le répertoire {$directory} n'est pas valide");
        }

        $path = $this->availableDirs[$directory] . $filename;
        
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // Retourner l'URL de l'image par défaut pour ce répertoire
        return $this->getDefaultImageUrl($directory);
    }

    /**
     * Retourne l'URL de l'image par défaut pour un répertoire
     */
    public function getDefaultImageUrl(string $directory): string
    {
        if (!isset($this->availableDirs[$directory])) {
            throw new \InvalidArgumentException("Le répertoire {$directory} n'est pas valide");
        }

        $defaultImagePath = $this->availableDirs[$directory] . 'default.jpg';
        
        // Si une image par défaut existe pour ce répertoire spécifique
        if (Storage::disk('public')->exists($defaultImagePath)) {
            return Storage::disk('public')->url($defaultImagePath);
        }

        // Sinon, utiliser l'image par défaut générique
        return asset('storage/images/default.jpg');
    }

    /**
     * Enregistre une image téléchargée
     *
     * @param \Illuminate\Http\UploadedFile|null $file Fichier téléchargé
     * @param string $directory Répertoire cible (doit être une clé de $availableDirs)
     * @param string|null $oldImage Nom de l'ancien fichier à supprimer (optionnel)
     * @param boolean $generateThumbnail Générer une miniature
     * @return string|null Nom du fichier enregistré ou null si aucun fichier
     */
    public function saveImage($file, string $directory, ?string $oldImage = null, bool $generateThumbnail = false): ?string
    {
        // Si aucun fichier n'est fourni, retourner null
        if (!$file) {
            return null;
        }

        // Supprimer l'ancienne image si nécessaire
        if ($oldImage) {
            $this->deleteImage($directory, $oldImage);
        }

        // Générer un nom de fichier unique
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Obtenir le chemin de stockage
        $storagePath = $this->getStoragePath($directory);
        
        // Stocker l'image
        $file->storeAs($storagePath, $filename, 'public');

        // Générer une miniature si demandé
        if ($generateThumbnail && in_array($directory, ['products', 'blog', 'categories'])) {
            $this->generateThumbnail($directory, $filename);
        }

        return $filename;
    }

    /**
     * Génère une miniature pour une image
     */
    public function generateThumbnail(string $directory, string $filename): void
    {
        // Définir les tailles en fonction du répertoire
        $sizes = [
            'products' => [300, 300],
            'blog' => [400, 300],
            'categories' => [300, 200],
        ];

        if (!isset($sizes[$directory])) {
            return;
        }

        // Définir le répertoire cible pour la miniature
        $thumbnailDirectory = $directory . '_thumbnails';
        
        // Vérifier si le répertoire de miniatures existe
        if (!isset($this->availableDirs[$thumbnailDirectory])) {
            return;
        }

        // Obtenir le chemin de l'image source
        $sourcePath = Storage::disk('public')->path($this->availableDirs[$directory] . $filename);
        
        // Obtenir le chemin de la miniature
        $targetPath = Storage::disk('public')->path($this->availableDirs[$thumbnailDirectory] . $filename);
        
        // Créer le répertoire de destination s'il n'existe pas
        $thumbnailDirPath = dirname($targetPath);
        if (!File::isDirectory($thumbnailDirPath)) {
            File::makeDirectory($thumbnailDirPath, 0755, true);
        }

        // Créer la miniature en utilisant Intervention Image
        $img = Image::make($sourcePath);
        $img->fit($sizes[$directory][0], $sizes[$directory][1], function ($constraint) {
            $constraint->upsize();
        });
        $img->save($targetPath);
    }

    /**
     * Supprime une image et ses miniatures associées
     */
    public function deleteImage(string $directory, string $filename): bool
    {
        if (!$filename || !isset($this->availableDirs[$directory])) {
            return false;
        }

        $path = $this->availableDirs[$directory] . $filename;
        $deleted = Storage::disk('public')->delete($path);

        // Supprimer aussi la miniature si elle existe
        $thumbnailDirectory = $directory . '_thumbnails';
        if (isset($this->availableDirs[$thumbnailDirectory])) {
            $thumbnailPath = $this->availableDirs[$thumbnailDirectory] . $filename;
            Storage::disk('public')->delete($thumbnailPath);
        }

        return $deleted;
    }

    /**
     * Crée une image de test (utile pour le développement)
     */
    public function createTestImage(string $directory, string $text = 'Test'): string
    {
        // Vérifier si le répertoire est valide
        if (!isset($this->availableDirs[$directory])) {
            throw new \InvalidArgumentException("Le répertoire {$directory} n'est pas valide");
        }

        // Créer une image avec Intervention Image
        $img = Image::canvas(800, 600, '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT));
        $img->text($text, 400, 300, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(48);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Générer un nom de fichier unique
        $filename = 'test_' . Str::uuid() . '.jpg';
        
        // Chemin de stockage
        $storagePath = Storage::disk('public')->path($this->availableDirs[$directory] . $filename);
        
        // Créer le répertoire s'il n'existe pas
        $dirPath = dirname($storagePath);
        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        // Sauvegarder l'image
        $img->save($storagePath);

        return $filename;
    }

    /**
     * Vérifie si tous les répertoires d'images existent et crée des images par défaut si nécessaire
     */
    public function ensureImageDirectories(): void
    {
        foreach ($this->availableDirs as $directory => $path) {
            $fullPath = Storage::disk('public')->path($path);
            
            // Créer le répertoire s'il n'existe pas
            if (!File::isDirectory($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }
            
            // Vérifier si une image par défaut existe
            $defaultImagePath = $path . 'default.jpg';
            if (!Storage::disk('public')->exists($defaultImagePath)) {
                // Créer une image par défaut
                $this->createDefaultImage($directory);
            }
        }
    }

    /**
     * Crée une image par défaut pour un répertoire
     */
    private function createDefaultImage(string $directory): void
    {
        // Définir des dimensions en fonction du répertoire
        $dimensions = [
            'products' => [800, 800],
            'product_thumbnails' => [300, 300],
            'product_large' => [1200, 1200],
            'categories' => [800, 400],
            'category_icons' => [64, 64],
            'category_banners' => [1920, 300],
            'users' => [300, 300],
            'user_avatars' => [150, 150],
            'user_covers' => [1200, 300],
            'banners' => [1920, 400],
            'promotions' => [800, 400],
            'blog' => [1200, 600],
            'blog_thumbnails' => [400, 300],
            'logos' => [200, 80],
            'icons' => [32, 32],
            'backgrounds' => [500, 500],
            'accueil' => [1920, 600],
        ];

        // Utiliser des dimensions par défaut si non définies
        $width = $dimensions[$directory][0] ?? 800;
        $height = $dimensions[$directory][1] ?? 600;

        // Créer l'image
        $img = Image::canvas($width, $height, '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT));
        $img->text(ucfirst(str_replace('_', ' ', $directory)), $width/2, $height/2, function($font) {
            $font->size(min($width, $height) / 10);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Chemin de stockage
        $storagePath = Storage::disk('public')->path($this->availableDirs[$directory] . 'default.jpg');
        
        // Créer le répertoire s'il n'existe pas
        $dirPath = dirname($storagePath);
        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        // Sauvegarder l'image
        $img->save($storagePath);
    }

    /**
     * Crée une image de test avec un format spécifique
     */
    public function createTestImage(int $width = 800, int $height = 600, string $text = null): string
    {
        $text = $text ?? 'Test Image';
        $image = Image::canvas($width, $height, '#f0f0f0');
        
        // Add text
        $image->text($text, $width / 2, $height / 2, function ($font) {
            $font->size(72);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Add a white background for better text readability
        $textBox = [
            'width' => $width * 0.9,
            'height' => 120,
            'x' => $width * 0.05,
            'y' => ($height / 2) - 60,
        ];

        $image->rectangle(
            $textBox['x'],
            $textBox['y'],
            $textBox['x'] + $textBox['width'],
            $textBox['y'] + $textBox['height'],
            function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.9)');
            }
        );

        // Add text over the white background
        $image->text($text, $width / 2, $height / 2, function ($font) {
            $font->size(72);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Generate unique filename
        $filename = $this->normalizeImageName('test-image', self::DEFAULT_FORMAT);
        $path = 'products/' . $filename;

        // Save image
        Storage::disk('public')->put($path, $image->encode(self::DEFAULT_FORMAT, self::DEFAULT_QUALITY));

        return $path;
    }

    /**
     * Normalise le nom d'un fichier image selon la convention
     */
    public function normalizeImageName(string $baseName, string $extension): string
    {
        $extension = strtolower($extension);
        if (!in_array($extension, self::ALLOWED_FORMATS)) {
            $extension = self::DEFAULT_FORMAT;
        }

        $baseName = Str::slug($baseName);
        $uniqueId = Str::random(8);

        return sprintf('%s-%s.%s', $baseName, $uniqueId, $extension);
    }

    /**
     * Convertit et optimise une image existante
     */
    public function convertImage(string $sourcePath, string $targetFormat = self::DEFAULT_FORMAT, int $quality = self::DEFAULT_QUALITY): ?string
    {
        try {
            if (!Storage::disk('public')->exists($sourcePath)) {
                return null;
            }

            $image = Image::make(Storage::disk('public')->path($sourcePath));
            $pathInfo = pathinfo($sourcePath);
            $newFilename = $this->normalizeImageName($pathInfo['filename'], $targetFormat);
            $newPath = $pathInfo['dirname'] . '/' . $newFilename;

            Storage::disk('public')->put(
                $newPath,
                $image->encode($targetFormat, $quality)
            );

            return $newPath;
        } catch (\Exception $e) {
            \Log::error('Image conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    public function generateThumbnails(string $sourcePath): array
    {
        $thumbnails = [];

        try {
            if (!Storage::disk('public')->exists($sourcePath)) {
                return $thumbnails;
            }

            $image = Image::make(Storage::disk('public')->path($sourcePath));
            $pathInfo = pathinfo($sourcePath);

            foreach (self::IMAGE_SIZES as $size => $dimensions) {
                $newFilename = sprintf(
                    '%s-%s.%s',
                    $pathInfo['filename'],
                    $size,
                    self::DEFAULT_FORMAT
                );
                $newPath = $pathInfo['dirname'] . '/' . $newFilename;

                // Create thumbnail
                $thumbnail = clone $image;
                $thumbnail->fit($dimensions['width'], $dimensions['height']);

                Storage::disk('public')->put(
                    $newPath,
                    $thumbnail->encode(self::DEFAULT_FORMAT, self::DEFAULT_QUALITY)
                );

                $thumbnails[$size] = [
                    'path' => $newPath,
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation failed: ' . $e->getMessage());
        }

        return $thumbnails;
    }
} 